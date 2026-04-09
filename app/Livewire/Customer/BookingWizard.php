<?php

namespace App\Livewire\Customer;

use App\Models\Service;
use App\Models\Package;
use App\Models\AddOn;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Coupon;
use Livewire\Component;

class BookingWizard extends Component
{
    public int $step = 1;
    public int $maxStepReached = 1;
    public string $bookingType = 'package';
    public ?int $selectedPackageId = null;
    public ?int $selectedServiceId = null;
    public array $selectedAddOns = [];
    public ?int $selectedAddressId = null;
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

    public function getSubtotal(): float
    {
        $total = 0;
        if ($this->bookingType === 'package' && $this->selectedPackageId) {
            $total = (float) (Package::find($this->selectedPackageId)?->price ?? 0);
        } elseif ($this->selectedServiceId) {
            $total = (float) (Service::find($this->selectedServiceId)?->base_price ?? 0);
        }
        foreach ($this->selectedAddOns as $id) {
            $total += (float) (AddOn::find($id)?->price ?? 0);
        }
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

        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'customer_id' => $user->id,
            'package_id' => $this->bookingType === 'package' ? $this->selectedPackageId : null,
            'service_id' => $this->bookingType === 'service' ? $this->selectedServiceId : null,
            'address_id' => $addressId,
            'coupon_id' => $this->couponValid ? Coupon::where('code', strtoupper(trim($this->couponCode)))->first()?->id : null,
            'status' => 'pending',
            'subtotal' => $this->getSubtotal(),
            'discount_amount' => $this->getDiscount(),
            'total_amount' => $this->getTotal(),
            'scheduled_date' => $this->scheduledDate,
            'scheduled_time' => $this->scheduledTime ?: null,
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
            'notes' => 'Booking placed by customer.',
        ]);

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
            4 => $this->validate([
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
