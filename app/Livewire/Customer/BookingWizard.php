<?php

namespace App\Livewire\Customer;

use App\Jobs\DispatchDaimaaJob;
use App\Models\Service;
use App\Models\Package;
use App\Models\AddOn;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\BookingSession;
use App\Models\Coupon;
use App\Services\GeocodingService;
use Livewire\Component;

class BookingWizard extends Component
{
    public int $step = 1;
    public int $maxStepReached = 1;
    public string $bookingType = 'package';
    public ?int $selectedPackageId = null;
    public ?int $selectedServiceId = null;
    public float $selectedHours = 1.0;
    public array $selectedAddOns = [];
    public ?int $selectedAddressId = null;
    public string $scheduleMode = 'schedule'; // 'instant' or 'schedule'
    public string $scheduledDate = '';
    public string $scheduledTime = '';
    public string $couponCode = '';
    public ?string $couponMessage = null;
    public bool $couponValid = false;
    public string $notes = '';

    public bool $newAddress = false;
    public string $addressLine1 = '';
    public string $addressLine2 = '';
    public string $landmark = '';
    public string $pincode = '';
    public string $addressLabel = 'Home';

    public function mount()
    {
        $this->scheduledDate = now()->addDays(2)->format('Y-m-d');
        if (auth()->user()->addresses()->count() === 0) {
            $this->newAddress = true;
        }
    }

    public function nextStep()
    {
        $this->validateStep();
        $this->step = min($this->step + 1, 5);
        $this->maxStepReached = max($this->maxStepReached, $this->step);
        $this->dispatch('stepChanged');
    }

    public function prevStep()
    {
        $this->step = max($this->step - 1, 1);
        $this->dispatch('stepChanged');
    }

    public function goToStep(int $step)
    {
        if ($step <= $this->maxStepReached) {
            $this->step = $step;
            $this->dispatch('stepChanged');
        }
    }

    public function selectPackage(int $id)
    {
        $this->selectedPackageId = $id;
        $this->selectedServiceId = null;
    }

    public function selectService(int $id)
    {
        $this->selectedServiceId = $id;
        $this->selectedPackageId = null;

        $service = Service::find($id);
        if ($service && $service->isHourlyPriced()) {
            $this->selectedHours = (float) $service->min_hours;
        } else {
            $this->selectedHours = 1.0;
        }
    }

    public function incrementHours()
    {
        $service = Service::find($this->selectedServiceId);
        if (!$service || !$service->isHourlyPriced()) return;

        $increment = (float) $service->hour_increment;
        $max = (float) $service->max_hours;
        $this->selectedHours = min($this->selectedHours + $increment, $max);
    }

    public function decrementHours()
    {
        $service = Service::find($this->selectedServiceId);
        if (!$service || !$service->isHourlyPriced()) return;

        $increment = (float) $service->hour_increment;
        $min = (float) $service->min_hours;
        $this->selectedHours = max($this->selectedHours - $increment, $min);
    }

    public function toggleAddOn(int $id)
    {
        if (in_array($id, $this->selectedAddOns)) {
            $this->selectedAddOns = array_values(array_diff($this->selectedAddOns, [$id]));
        } else {
            $this->selectedAddOns[] = $id;
        }
    }

    public function applyCoupon()
    {
        if (empty(trim($this->couponCode))) {
            $this->couponMessage = 'Please enter a coupon code.';
            $this->couponValid = false;
            return;
        }
        $coupon = Coupon::where('code', strtoupper(trim($this->couponCode)))->first();
        if ($coupon && $coupon->isValid()) {
            $this->couponValid = true;
            $this->couponMessage = 'Coupon applied! You save ₹' . number_format($this->getDiscount($coupon));
        } else {
            $this->couponValid = false;
            $this->couponMessage = 'Invalid or expired coupon code.';
        }
    }

    public function removeCoupon()
    {
        $this->couponCode = '';
        $this->couponValid = false;
        $this->couponMessage = null;
    }

    public function getSelectedName(): string
    {
        if ($this->bookingType === 'package' && $this->selectedPackageId) {
            return Package::find($this->selectedPackageId)?->name ?? '';
        }
        if ($this->bookingType === 'service' && $this->selectedServiceId) {
            return Service::find($this->selectedServiceId)?->name ?? '';
        }
        return '';
    }

    public function getServicePrice(): float
    {
        if ($this->bookingType === 'service' && $this->selectedServiceId) {
            $service = Service::find($this->selectedServiceId);
            if ($service && $service->isHourlyPriced()) {
                return $service->getPriceForHours($this->selectedHours);
            }
            return (float) ($service?->base_price ?? 0);
        }
        return 0;
    }

    public function getInstantSurcharge(): float
    {
        if ($this->scheduleMode !== 'instant') return 0;

        if ($this->bookingType === 'service' && $this->selectedServiceId) {
            $service = Service::find($this->selectedServiceId);
            if ($service && $service->instant_available) {
                return (float) ($service->instant_surcharge ?? 0);
            }
        }

        if ($this->bookingType === 'package' && $this->selectedPackageId) {
            $package = Package::find($this->selectedPackageId);
            if ($package) {
                $surcharge = 0;
                foreach ($package->services as $svc) {
                    if ($svc->instant_available) {
                        $surcharge = max($surcharge, (float) ($svc->instant_surcharge ?? 0));
                    }
                }
                return $surcharge;
            }
        }

        return 0;
    }

    public function isInstantAvailable(): bool
    {
        if ($this->bookingType === 'service' && $this->selectedServiceId) {
            $service = Service::find($this->selectedServiceId);
            return $service && $service->instant_available;
        }
        if ($this->bookingType === 'package' && $this->selectedPackageId) {
            $package = Package::find($this->selectedPackageId);
            return $package && $package->services->contains(fn ($s) => $s->instant_available);
        }
        return false;
    }

    public function getSubtotal(): float
    {
        $total = 0;
        if ($this->bookingType === 'package' && $this->selectedPackageId) {
            $total = (float) (Package::find($this->selectedPackageId)?->price ?? 0);
        } elseif ($this->selectedServiceId) {
            $total = $this->getServicePrice();
        }
        foreach ($this->selectedAddOns as $id) {
            $total += (float) (AddOn::find($id)?->price ?? 0);
        }
        $total += $this->getInstantSurcharge();
        return $total;
    }

    public function getDiscount(?Coupon $coupon = null): float
    {
        if (! $this->couponValid) return 0;
        $coupon = $coupon ?? Coupon::where('code', strtoupper(trim($this->couponCode)))->first();
        if (! $coupon) return 0;
        $subtotal = $this->getSubtotal();
        $discount = $coupon->type === 'percent' ? $subtotal * ($coupon->value / 100) : (float) $coupon->value;
        if ($coupon->max_discount) {
            $discount = min($discount, (float) $coupon->max_discount);
        }
        return $discount;
    }

    public function getTotal(): float
    {
        return max(0, $this->getSubtotal() - $this->getDiscount());
    }

    public function placeBooking()
    {
        $user = auth()->user();
        $addressId = $this->selectedAddressId;

        if ($this->newAddress) {
            $this->validate([
                'addressLine1' => 'required|string|max:255',
                'pincode' => 'required|string|size:6',
            ]);
            $address = $user->addresses()->create([
                'label' => $this->addressLabel,
                'address_line_1' => $this->addressLine1,
                'address_line_2' => $this->addressLine2,
                'landmark' => $this->landmark,
                'city_id' => 1,
                'pincode' => $this->pincode,
            ]);
            $addressId = $address->id;
        }

        if (! $addressId) {
            $this->addError('selectedAddressId', 'Please select or add an address.');
            return;
        }

        $bookedHours = null;
        if ($this->bookingType === 'service' && $this->selectedServiceId) {
            $svc = Service::find($this->selectedServiceId);
            if ($svc && $svc->isHourlyPriced()) {
                $bookedHours = $this->selectedHours;
            }
        }

        $isInstant = $this->scheduleMode === 'instant';
        $scheduledDate = $isInstant ? now()->toDateString() : $this->scheduledDate;
        $scheduledTime = $isInstant ? now()->addMinutes(30)->format('H:i') : ($this->scheduledTime ?: null);

        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'customer_id' => $user->id,
            'package_id' => $this->bookingType === 'package' ? $this->selectedPackageId : null,
            'service_id' => $this->bookingType === 'service' ? $this->selectedServiceId : null,
            'booked_hours' => $bookedHours,
            'is_instant' => $isInstant,
            'address_id' => $addressId,
            'coupon_id' => $this->couponValid ? Coupon::where('code', strtoupper(trim($this->couponCode)))->first()?->id : null,
            'status' => 'pending',
            'subtotal' => $this->getSubtotal(),
            'discount_amount' => $this->getDiscount(),
            'total_amount' => $this->getTotal(),
            'scheduled_date' => $scheduledDate,
            'scheduled_time' => $scheduledTime,
            'notes' => $this->notes,
        ]);

        foreach ($this->selectedAddOns as $addOnId) {
            $addOn = AddOn::find($addOnId);
            if ($addOn) {
                BookingItem::create([
                    'booking_id' => $booking->id,
                    'itemable_type' => AddOn::class,
                    'itemable_id' => $addOnId,
                    'quantity' => 1,
                    'unit_price' => $addOn->price,
                    'total_price' => $addOn->price,
                ]);
            }
        }

        $booking->statusHistories()->create([
            'from_status' => null,
            'to_status' => 'pending',
            'changed_by' => $user->id,
            'notes' => $isInstant ? 'Instant booking placed — Daimaa expected within 30 minutes.' : 'Booking placed by customer.',
        ]);

        if ($this->bookingType === 'package' && $this->selectedPackageId) {
            $package = Package::with('services')->find($this->selectedPackageId);
            if ($package) {
                $sessionNumber = 1;
                $firstSessionScheduledAt = $scheduledDate && $scheduledTime
                    ? \Carbon\Carbon::parse("$scheduledDate $scheduledTime")
                    : ($scheduledDate ? \Carbon\Carbon::parse($scheduledDate)->setHour(9) : null);

                foreach ($package->services as $svc) {
                    $count = $svc->pivot->session_count ?? 1;
                    for ($i = 0; $i < $count; $i++) {
                        $isFirstSession = $sessionNumber === 1;
                        BookingSession::create([
                            'booking_id' => $booking->id,
                            'service_id' => $svc->id,
                            'session_number' => $sessionNumber,
                            'scheduled_at' => $isFirstSession ? $firstSessionScheduledAt : null,
                            'status' => $isFirstSession && $firstSessionScheduledAt ? 'scheduled' : 'upcoming',
                        ]);
                        $sessionNumber++;
                    }
                }
            }
        } elseif ($this->bookingType === 'service' && $this->selectedServiceId) {
            $scheduledAt = $scheduledDate && $scheduledTime
                ? \Carbon\Carbon::parse("$scheduledDate $scheduledTime")
                : ($scheduledDate ? \Carbon\Carbon::parse($scheduledDate)->setHour(9) : null);

            BookingSession::create([
                'booking_id' => $booking->id,
                'service_id' => $this->selectedServiceId,
                'session_number' => 1,
                'scheduled_at' => $scheduledAt,
                'status' => $scheduledAt ? 'scheduled' : 'upcoming',
            ]);
        }

        // Geocode the address in the background (best-effort)
        if ($booking->address && !$booking->address->latitude) {
            $coords = GeocodingService::geocode(
                $booking->address->address_line_1 ?? '',
                $booking->address->pincode ?? '',
                $booking->address->city?->name ?? ''
            );
            if ($coords) {
                $booking->address->update($coords);
            }
        }

        // Auto-dispatch: find and assign best available Daimaa
        if (config('daimaa_matching.enabled', true)) {
            $delay = $isInstant ? 0 : 5; // instant = immediate, scheduled = 5 sec buffer
            DispatchDaimaaJob::dispatch($booking->id)->delay(now()->addSeconds($delay));
        }

        session()->flash('success', 'Booking placed successfully! Booking #' . $booking->booking_number);
        return redirect()->route('customer.bookings');
    }

    protected function validateStep(): void
    {
        match ($this->step) {
            1 => $this->bookingType === 'package'
                ? $this->validate(['selectedPackageId' => 'required|exists:packages,id'], ['selectedPackageId.required' => 'Please select a package to continue.'])
                : $this->validate(['selectedServiceId' => 'required|exists:services,id'], ['selectedServiceId.required' => 'Please select a service to continue.']),
            3 => $this->newAddress
                ? $this->validate([
                    'addressLine1' => 'required|string|max:255',
                    'pincode' => 'required|string|size:6',
                ], [
                    'addressLine1.required' => 'Address is required.',
                    'pincode.required' => 'Pincode is required.',
                    'pincode.size' => 'Pincode must be 6 digits.',
                ])
                : $this->validate(['selectedAddressId' => 'required|exists:addresses,id'], ['selectedAddressId.required' => 'Please select an address.']),
            4 => $this->scheduleMode === 'instant'
                ? null
                : $this->validate([
                    'scheduledDate' => 'required|date|after:today',
                ], [
                    'scheduledDate.after' => 'Please select a future date.',
                ]),
            default => null,
        };
    }

    public function render()
    {
        return view('livewire.customer.booking-wizard', [
            'packages' => Package::where('is_active', true)->with('services')->orderBy('sort_order')->get(),
            'services' => Service::where('is_active', true)->with('category')->orderBy('sort_order')->get(),
            'addOns' => AddOn::where('is_active', true)->get(),
            'addresses' => auth()->user()->addresses()->with('city')->get(),
        ]);
    }
}
