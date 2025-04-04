(function () {
    let paymentMethod = '';
    let baseTotal = window.baseTotal || 0;
    let finalTotal = window.finalTotal || 0;
    let stripeFee = 0;
    let walletUsed = window.walletUsed || 0;
    let isWalletUsed = window.isWalletUsed || false;

    // Елементи DOM
    const walletCheckbox = document.getElementById('wallet-pay');
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const stripeFeeBox = document.querySelector('.stripe-fee-box');
    const stripeFeeElement = document.getElementById('stripe-fee');
    const finalTotalElement = document.getElementById('show-final-total');
    const finalTotalInput = document.getElementById('final_total');
    const isWalletUsedInput = document.getElementById('is_wallet_used');
    const walletBalanceUsedInput = document.getElementById('wallet_balance_used');

    // Ініціалізація
    console.log('Checkout init');

    // Обробник зміни чекбокса гаманця
    walletCheckbox.addEventListener('change', () => {
        isWalletUsed = walletCheckbox.checked;
        updateTotals();
    });

    // Обробник зміни способу оплати
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            paymentMethod = radio.value;
            updateTotals();
        });
    });

    // Функція для оновлення підсумків
    function updateTotals() {
        const walletBalance = parseFloat(walletCheckbox.dataset.walletBalance) || 0;
        walletUsed = isWalletUsed ? Math.min(walletBalance, baseTotal) : 0;
        const amountToPay = baseTotal - walletUsed;
        console.log('Checked payment method = ', paymentMethod);
        if (paymentMethod === 'stripe') {
            const appUrl = document.getElementById('app_url')?.dataset.appUrl || window.location.origin;
            const formData = new FormData();
            formData.append('amount', amountToPay);

            fetch(`${appUrl}/payments/stripe/calculate-fee`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.error && data.fee && data.total_with_fee) {
                        stripeFee = parseFloat(data.fee) || 0;
                        finalTotal = baseTotal - walletUsed + stripeFee;

                        stripeFeeBox.classList.remove('d-none');
                        stripeFeeElement.textContent = formatCurrency(stripeFee);
                        finalTotalElement.textContent = formatCurrency(finalTotal);
                        finalTotalInput.value = finalTotal;

                        isWalletUsedInput.value = isWalletUsed ? 1 : 0;
                        walletBalanceUsedInput.value = walletUsed;
                    } else {
                        resetStripeFee();
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    resetStripeFee();
                });
        } else {
            finalTotal = baseTotal - walletUsed;
            resetStripeFee();

            finalTotalElement.textContent = formatCurrency(finalTotal);
            finalTotalInput.value = finalTotal;

            isWalletUsedInput.value = isWalletUsed ? 1 : 0;
            walletBalanceUsedInput.value = walletUsed;
        }
    }

    // Функція для скидання комісії Stripe
    function resetStripeFee() {
        stripeFee = 0;
        stripeFeeBox.classList.add('d-none');
        stripeFeeElement.textContent = '';
    }

    // Функція форматування валюти
    function formatCurrency(amount) {
        const currencySymbol = document.getElementById('currency_code').value || '$';
        return `${currencySymbol}${parseFloat(amount).toFixed(2)}`;
    }

    // Початкове оновлення підсумків
    updateTotals();
})();
