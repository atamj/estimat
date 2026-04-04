<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use Livewire\Component;

class CouponManager extends Component
{
    public $coupons;

    public $code;

    public $type = 'percentage';

    public $value;

    public $expires_at;

    public $max_uses;

    public $is_active = true;

    public $editingCouponId = null;

    public $showForm = false;

    protected $rules = [
        'code' => 'required|string|unique:coupons,code',
        'type' => 'required|in:percentage,fixed',
        'value' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->loadCoupons();
    }

    public function loadCoupons()
    {
        $this->coupons = Coupon::all();
    }

    public function toggleForm()
    {
        $this->showForm = ! $this->showForm;
        if (! $this->showForm) {
            $this->resetFields();
        }
    }

    public function resetFields()
    {
        $this->code = '';
        $this->type = 'percentage';
        $this->value = '';
        $this->expires_at = '';
        $this->max_uses = '';
        $this->is_active = true;
        $this->editingCouponId = null;
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->editingCouponId) {
            $rules['code'] = 'required|string|unique:coupons,code,'.$this->editingCouponId;
        }
        $this->validate($rules);

        $data = [
            'code' => strtoupper($this->code),
            'type' => $this->type,
            'value' => $this->value,
            'expires_at' => $this->expires_at ?: null,
            'max_uses' => $this->max_uses ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingCouponId) {
            Coupon::findOrFail($this->editingCouponId)->update($data);
            session()->flash('message', 'Code promo mis à jour avec succès.');
        } else {
            Coupon::create($data);
            session()->flash('message', 'Code promo créé avec succès.');
        }

        $this->resetFields();
        $this->showForm = false;
        $this->loadCoupons();
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        $this->editingCouponId = $id;
        $this->code = $coupon->code;
        $this->type = $coupon->type;
        $this->value = $coupon->value;
        $this->expires_at = $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '';
        $this->max_uses = $coupon->max_uses;
        $this->is_active = $coupon->is_active;
        $this->showForm = true;
    }

    public function delete($id)
    {
        Coupon::findOrFail($id)->delete();
        $this->loadCoupons();
        session()->flash('message', 'Code promo supprimé avec succès.');
    }

    public function render()
    {
        return view('livewire.admin.coupon-manager');
    }
}
