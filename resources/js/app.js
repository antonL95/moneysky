import './bootstrap';

document.addEventListener('alpine:init', () => {
    Alpine.data('cookieConsent', () => ({
        consentOpen: !userDidConsent(),

        toggle() {
            setConsentCookie();

            this.consentOpen = !this.consentOpen;
        },
    }));
});

function userDidConsent() {
    return document.cookie.split(';').indexOf(`name=cookie-consent`) !== -1;
}
