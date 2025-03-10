<div>
    <div wire:ignore class="input-group input-group-merge">
        <input wire:model="mobile" type="text" id="mobile" name="mobile" maxlength="16" class="form-control w-full"
            value="{{ $mobile }}" />
    </div>
    @error('mobile')
        <span class="error text-danger">{{ $message }}</span>
    @enderror
    @error('phone_full')
        <span class="error text-danger">{{ $message }}</span>
    @enderror
    @error('country_code')
        <span class="error text-danger">{{ $message }}</span>
    @enderror
    <input type="hidden" wire:model="country_code" name="country_code" id="country_code" />
    <input type="hidden" wire:model="phone_full" name="phone_full" id="phone_full" />

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/css/intlTelInput.css">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/intlTelInput.min.js"></script>
    @endpush

    @script
        <script>
            document.addEventListener('livewire:init', function() {
                const mobile = document.querySelector("#mobile");
                const phone_full = document.querySelector("#phone_full");
                const country_code = document.querySelector("#country_code");

                const iti = window.intlTelInput(mobile, {
                    allowExtensions: true,
                    formatOnDisplay: true,
                    autoFormat: true,
                    autoHideDialCode: false,
                    autoPlaceholder: "polite",
                    defaultCountry: "in",
                    ipinfoToken: "yolo",
                    nationalMode: true,
                    numberType: "MOBILE",
                    preventInvalidNumbers: true,
                    separateDialCode: true,
                    initialCountry: "auto",
                    geoIpLookup: callback => {
                        fetch("https://ipapi.co/json")
                            .then(res => res.json())
                            .then(data => callback(data.country_code))
                            .catch(() => callback("us"));
                    },
                    loadUtils: () => import(
                        "https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/utils.js")
                });

                const handleChange = () => {
                    if (mobile.value) {
                        if (iti.isValidNumber()) {
                            @this.set('phone_full', iti.getNumber().replace('+' + iti.getSelectedCountryData()
                                .dialCode, ''));
                            @this.set('country_code', iti.getSelectedCountryData().dialCode);
                        }
                    }
                };

                mobile.addEventListener('change', handleChange);
                mobile.addEventListener('keyup', handleChange);
            });
        </script>
    @endscript
</div>
