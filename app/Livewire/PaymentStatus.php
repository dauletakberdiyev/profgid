<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TestSession;

class PaymentStatus extends Component
{
    public $sessionId;
    public $plan;
    public $testSession;
    public $payerName;
    public $paymentConfirmed = false;
    
    public $plans = [
        'talents' => [
            'name' => 'Таланты',
            'price' => 3000,
            'currency' => 'тг'
        ],
        'talents_spheres' => [
            'name' => 'Таланты + Топ сферы',
            'price' => 6000,
            'currency' => 'тг'
        ],
        'talents_spheres_professions' => [
            'name' => 'Таланты + Топ сферы + Топ профессии',
            'price' => 9000,
            'currency' => 'тг'
        ]
    ];

    protected $listeners = ['refreshPaymentStatus'];

    public function mount($sessionId, $plan)
    {
        $this->sessionId = $sessionId;
        $this->plan = $plan;
        
        $this->testSession = TestSession::where('session_id', $this->sessionId)->first();
        
        if (!$this->testSession) {
            session()->flash('error', 'Сессия теста не найдена');
            return redirect()->route('payment');
        }
        
        // Проверяем, подтверждена ли уже оплата
        $this->paymentConfirmed = !empty($this->testSession->payer_name);
        $this->payerName = $this->testSession->payer_name;
    }

    public function refreshPaymentStatus()
    {
        $this->testSession = $this->testSession->fresh();
        $this->paymentConfirmed = !empty($this->testSession->payer_name);
        $this->payerName = $this->testSession->payer_name;
    }

    public function confirmPayment()
    {
        $this->validate([
            'payerName' => 'required|string|min:2|max:255'
        ], [
            'payerName.required' => 'Пожалуйста, укажите имя плательщика',
            'payerName.min' => 'Имя должно содержать минимум 2 символа',
            'payerName.max' => 'Имя не должно превышать 255 символов'
        ]);
        
        // Обновляем сессию
        $this->testSession->update([
            'payer_name' => $this->payerName,
            'payment_status' => 'review'
        ]);
        
        $this->paymentConfirmed = true;
        
        session()->flash('success', 'Информация о плательщике сохранена! Мы проверим оплату в течение 30 минут.');
    }

    public function getStatusText()
    {
        switch ($this->testSession->payment_status) {
            case 'pending':
                return 'Ожидание оплаты';
            case 'review':
                return 'Проверка оплаты';
            case 'completed':
                return 'Оплата подтверждена';
            case 'failed':
                return 'Ошибка оплаты';
            default:
                return 'Неизвестный статус';
        }
    }

    public function getStatusColor()
    {
        switch ($this->testSession->payment_status) {
            case 'pending':
                return 'text-yellow-600 bg-yellow-100';
            case 'review':
                return 'text-blue-600 bg-blue-100';
            case 'completed':
                return 'text-green-600 bg-green-100';
            case 'failed':
                return 'text-red-600 bg-red-100';
            default:
                return 'text-gray-600 bg-gray-100';
        }
    }

    public function render()
    {
        return view('livewire.payment-status');
    }
}
