<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\TestSession;
use Illuminate\Support\Facades\Storage;

class PaymentStatus extends Component
{
    use WithFileUploads;
    
    public $sessionId;
    public $plan;
    public $testSession;
    public $receiptImage;
    public $uploadedReceipt = false;
    
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
        
        // Проверяем, загружен ли уже чек
        $this->uploadedReceipt = !empty($this->testSession->receipt_image);
    }

    public function refreshPaymentStatus()
    {
        $this->testSession = $this->testSession->fresh();
        $this->uploadedReceipt = !empty($this->testSession->receipt_image);
    }

    public function confirmPayment()
    {
        $this->validate([
            'receiptImage' => 'required|image|max:5120' // 5MB max
        ], [
            'receiptImage.required' => 'Пожалуйста, прикрепите чек об оплате',
            'receiptImage.image' => 'Файл должен быть изображением',
            'receiptImage.max' => 'Размер файла не должен превышать 5MB'
        ]);

        // Сохраняем изображение
        $imagePath = $this->receiptImage->store('receipts', 'public');
        
        // Обновляем сессию
        $this->testSession->update([
            'receipt_image' => $imagePath,
            'payment_status' => 'review'
        ]);
        
        $this->uploadedReceipt = true;
        
        session()->flash('success', 'Чек загружен! Мы проверим оплату в течение 30 минут.');
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
