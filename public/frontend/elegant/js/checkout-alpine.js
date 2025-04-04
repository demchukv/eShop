document.addEventListener('alpine:init', () => {
    Alpine.data('checkout', () => ({
        payment_method: '',
        baseTotal: window.baseTotal || 0, // Передаємо через глобальну змінну
        finalTotal: window.finalTotal || 0,
        stripeFee: 0,
        walletUsed: window.walletUsed || 0,
        isWalletUsed: window.isWalletUsed || false,

        init() {
            console.log('Checkout init');
            this.$watch('payment_method', (value) => this.handlePaymentMethodChange(value));
            this.$watch('isWalletUsed', (value) => this.handleWalletChange(value));

            const walletCheckbox = document.getElementById('wallet-pay');
            walletCheckbox.addEventListener('change', () => {
                this.isWalletUsed = walletCheckbox.checked;
            });
        },

        handlePaymentMethodChange(payment_method) {
            this.updateTotals();
        },

        handleWalletChange(isWalletUsed) {
            this.updateTotals();
        },

        updateTotals() {
            const walletBalance = parseFloat(document.getElementById('wallet-pay').dataset.walletBalance) || 0;
            this.walletUsed = this.isWalletUsed ? Math.min(walletBalance, this.baseTotal) : 0;
            const amountToPay = this.baseTotal - this.walletUsed;

            if (this.payment_method === 'stripe') {
                const appUrl = document.getElementById("app_url")?.dataset.appUrl || window.location.origin;
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
                            this.stripeFee = parseFloat(data.fee) || 0;
                            this.finalTotal = this.baseTotal - this.walletUsed + this.stripeFee;

                            document.querySelector('.stripe-fee-box').classList.remove('d-none');
                            document.getElementById('stripe-fee').textContent = this.formatCurrency(this.stripeFee);
                            document.getElementById('show-final-total').textContent = this.formatCurrency(this.finalTotal);
                            document.getElementById('final_total').value = this.finalTotal;

                            document.getElementById('is_wallet_used').value = this.isWalletUsed ? 1 : 0;
                            document.getElementById('wallet_balance_used').value = this.walletUsed;
                        } else {
                            this.resetStripeFee();
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        this.resetStripeFee();
                    });
            } else {
                this.finalTotal = this.baseTotal - this.walletUsed;
                this.resetStripeFee();

                document.getElementById('show-final-total').textContent = this.formatCurrency(this.finalTotal);
                document.getElementById('final_total').value = this.finalTotal;

                document.getElementById('is_wallet_used').value = this.isWalletUsed ? 1 : 0;
                document.getElementById('wallet_balance_used').value = this.walletUsed;
            }
        },

        resetStripeFee() {
            this.stripeFee = 0;
            document.querySelector('.stripe-fee-box').classList.add('d-none');
            document.getElementById('stripe-fee').textContent = '';
        },

        formatCurrency(amount) {
            const currencySymbol = document.getElementById('currency_code').value || '$';
            return `${currencySymbol}${parseFloat(amount).toFixed(2)}`;
        }
    }));
});