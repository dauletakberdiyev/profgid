<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TestSession;
use App\Models\PromoCode;

class PaymentStatus extends Component
{
    public $sessionId;
    public $plan;
    public $testSession;
    public $pin1;
    public $pin2;
    public $pin3;
    public $pin4;
    public $pin5;
    public $pin6;
    public $paymentConfirmed = false;
    public $showPromoCodeForm = false;

    public $plans = [
        "talents" => [
            "name" => "Таланты",
            "price" => 3000,
            "currency" => "тг",
        ],
        "talents_spheres" => [
            "name" => "Таланты + Топ сферы",
            "price" => 6000,
            "currency" => "тг",
        ],
        "talents_spheres_professions" => [
            "name" => "Таланты + Топ сферы + Топ профессии",
            "price" => 9000,
            "currency" => "тг",
        ],
    ];

    // Removed static array - now using database

    protected $listeners = ["refreshPaymentStatus"];

    public function mount($sessionId, $plan)
    {
        $this->sessionId = $sessionId;
        $this->plan = $plan;

        $this->testSession = TestSession::where(
            "session_id",
            $this->sessionId
        )->first();

        if (!$this->testSession) {
            session()->flash("error", "Сессия теста не найдена");
            return redirect()->route("payment");
        }

        // Проверяем, подтверждена ли уже оплата
        $this->paymentConfirmed =
            $this->testSession->payment_status === "completed";
    }

    public function refreshPaymentStatus()
    {
        $this->testSession = $this->testSession->fresh();
        $this->paymentConfirmed =
            $this->testSession->payment_status === "completed";
    }

    public function getPromoCode()
    {
        $this->showPromoCodeForm = true;

        // Prepare WhatsApp link but don't redirect
        $phone = "+77072170555";
        $message = urlencode(
            "Здравствуйте! Хочу получить промокод для профориентационного теста."
        );
        $whatsappUrl = "https://wa.me/$phone?text=$message";

        // Emit event to open WhatsApp in new tab via JavaScript
        $this->dispatch("openWhatsApp", $whatsappUrl);
    }

    public function confirmPayment()
    {
        // Validate PIN inputs
        $this->validate(
            [
                "pin1" => "required|digits:1",
                "pin2" => "required|digits:1",
                "pin3" => "required|digits:1",
                "pin4" => "required|digits:1",
                "pin5" => "required|digits:1",
                "pin6" => "required|digits:1",
            ],
            [
                "pin*.required" => "Пожалуйста, заполните все поля промокода",
                "pin*.digits" => "Каждое поле должно содержать одну цифру",
            ]
        );

        // Combine PIN digits to form the promo code
        $promoCode =
            $this->pin1 .
            $this->pin2 .
            $this->pin3 .
            $this->pin4 .
            $this->pin5 .
            $this->pin6;

        // Find the promo code in database
        $promoCodeRecord = PromoCode::where("code", $promoCode)
            ->where("is_active", true)
            ->where("is_used", false)
            ->first();

        if ($promoCodeRecord) {
            // Mark promo code as used
            $promoCodeRecord->markAsUsed(auth()->id());

            // Update test session as completed
            $this->testSession->update([
                "payment_status" => "completed",
                "promo_code" => $promoCode,
            ]);

            $this->paymentConfirmed = true;

            session()->flash(
                "success",
                "Промокод принят! Оплата успешно подтверждена."
            );
        } else {
            session()->flash(
                "error",
                "Неверный промокод или промокод уже использован. Пожалуйста, проверьте правильность ввода."
            );
        }
    }

    public function getStatusText()
    {
        switch ($this->testSession->payment_status) {
            case "pending":
                return "Ожидание оплаты";
            case "review":
                return "Проверка оплаты";
            case "completed":
                return "Оплата подтверждена";
            case "failed":
                return "Ошибка оплаты";
            default:
                return "Неизвестный статус";
        }
    }

    public function getStatusColor()
    {
        switch ($this->testSession->payment_status) {
            case "pending":
                return "text-yellow-600 bg-yellow-100";
            case "review":
                return "text-blue-600 bg-blue-100";
            case "completed":
                return "text-green-600 bg-green-100";
            case "failed":
                return "text-red-600 bg-red-100";
            default:
                return "text-gray-600 bg-gray-100";
        }
    }

    public function render()
    {
        return view("livewire.payment-status");
    }
}
