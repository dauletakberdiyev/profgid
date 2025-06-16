<div class="min-h-screen bg-white py-8 md:py-16 px-4" wire:poll.30s="refreshPaymentStatus">
    <div class="max-w-4xl mx-auto">
        <!-- Status Header -->
        <div class="text-center mb-8 md:mb-12">
            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium mb-4 {{ $this->getStatusColor() }}">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    @if($testSession->payment_status === 'pending')
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    @elseif($testSession->payment_status === 'review')
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    @elseif($testSession->payment_status === 'completed')
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    @else
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    @endif
                </svg>
                {{ $this->getStatusText() }}
            </div>
            
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-light text-gray-900 mb-4">Оплата тарифа</h1>
            <h2 class="text-xl md:text-2xl font-medium text-gray-800 mb-2">{{ $plans[$plan]['name'] }}</h2>
            <p class="text-2xl md:text-3xl font-light text-gray-900">{{ number_format($plans[$plan]['price']) }} {{ $plans[$plan]['currency'] }}</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-8">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Payment Options -->
            <div class="bg-gray-50 rounded-2xl p-6 md:p-8">
                <h3 class="text-xl font-medium text-gray-900 mb-6">Способы оплаты</h3>
                
                <!-- QR Code -->
                <div class="bg-white rounded-xl p-6 mb-6 text-center">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">QR-код для оплаты</h4>
                    <div class="flex justify-center mb-4">
                        <img src="{{ asset('assets/images/kaspi-qr.png') }}" 
                             alt="Kaspi QR Code" 
                             class="w-48 h-48 rounded-xl border">
                    </div>
                    <p class="text-sm text-gray-600">Сканируйте QR-код в приложении Kaspi</p>
                </div>
                
                <!-- Divider -->
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-gray-50 text-gray-500">или</span>
                    </div>
                </div>
                
                <!-- Payment Link -->
                <a href="https://pay.kaspi.kz/pay/d9gpkmul" 
                   target="_blank" 
                   class="w-full inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 text-lg font-medium">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Перейти к оплате в Kaspi
                </a>
            </div>

            <!-- Upload Receipt -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8">
                <h3 class="text-xl font-medium text-gray-900 mb-6">Подтверждение оплаты</h3>
                
                @if(!$uploadedReceipt && $testSession->payment_status !== 'completed')
                    <form wire:submit.prevent="confirmPayment">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Прикрепите чек об оплате
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-gray-400 transition-colors">
                                <input type="file" 
                                       wire:model="receiptImage" 
                                       accept="image/*"
                                       class="hidden"
                                       id="receipt-upload">
                                <label for="receipt-upload" class="cursor-pointer">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <p class="text-gray-600">Нажмите, чтобы выбрать файл</p>
                                    <p class="text-sm text-gray-500 mt-1">PNG, JPG до 5MB</p>
                                </label>
                            </div>
                            @error('receiptImage') 
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        @if($receiptImage)
                            <div class="mb-6">
                                <p class="text-sm text-gray-600 mb-2">Выбранный файл:</p>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-sm font-medium">{{ $receiptImage->getClientOriginalName() }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <button type="submit" 
                                class="w-full py-3 px-6 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors font-medium">
                            Я оплатил(а)
                        </button>
                    </form>
                @elseif($testSession->payment_status === 'review')
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-blue-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Чек загружен</h4>
                        <p class="text-gray-600">Мы проверяем вашу оплату. Это займет до 30 минут.</p>
                    </div>
                @elseif($testSession->payment_status === 'completed')
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-green-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Оплата подтверждена!</h4>
                        <p class="text-gray-600 mb-4">Результаты теста доступны в вашем профиле</p>
                        <a href="{{ route('talent-test-results') }}" 
                           class="inline-flex items-center px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            Посмотреть результаты
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center">
            <a href="{{ route('profile') }}" 
               class="inline-flex items-center text-gray-600 hover:text-gray-800 font-light text-sm md:text-base transition-colors">
                <svg class="w-4 h-4 md:w-5 md:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Назад к профиль
            </a>
        </div>
    </div>
</div>
