import { PhoneCall } from 'lucide-react';

const SUPPORT_PHONE = '+91 8939905115';

const ContactBanners = () => {
  return (
    <section className="contact-banners-section">
      <div className="contact-banners-container">

        {/* Banner 1: WhatsApp Booking */}
        <div className="whatsapp-booking-banner">
          <h3 className="whatsapp-banner-title">Now you can book a test on whatsapp</h3>

          <div className="whatsapp-banner-bottom-row">
            <p className="whatsapp-banner-subtitle">
              Need Help With Home Collection Booking? Let's Connect On Whatsapp
            </p>
            <div className="whatsapp-banner-line"></div>

            <a href="https://wa.me/91 8939905115" target="_blank" rel="noopener noreferrer" className="whatsapp-pill-btn">
              <div className="whatsapp-icon-circle">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12.004 2C6.48 2 2 6.48 2 12C2 13.9 2.53 15.69 3.45 17.22L2 22L6.92 20.62C8.38 21.5 10.12 22 12.004 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12.004 2ZM17.21 15.93C16.99 16.55 16.1 17.07 15.42 17.21C14.96 17.3 14.36 17.37 12.33 16.53C9.74 15.46 8.07 12.83 7.94 12.66C7.81 12.49 6.87 11.24 6.87 9.95C6.87 8.66 7.52 8.03 7.79 7.76C8 7.49 8.44 7.38 8.8 7.38C8.95 7.38 9.09 7.39 9.21 7.39C9.56 7.41 9.74 7.43 9.98 8C10.28 8.73 11.02 10.54 11.11 10.73C11.2 10.92 11.29 11.18 11.17 11.43C11.05 11.68 10.95 11.8 10.77 12.01C10.59 12.22 10.42 12.37 10.24 12.58C10.08 12.77 9.9 12.98 10.1 13.33C10.3 13.68 11 14.83 12.03 15.75C13.36 16.93 14.45 17.31 14.85 17.48C15.25 17.65 15.49 17.61 15.71 17.36C15.93 17.11 16.66 16.25 16.89 15.92C17.12 15.59 17.35 15.65 17.69 15.77C18.03 15.89 19.86 16.8 20.23 16.99C20.6 17.18 20.85 17.27 20.94 17.43C21.03 17.59 21.03 18.37 20.7 18.99C20.47 19.43 19.58 19.85 18.8 19.95C18 20.05 17.44 20.08 15.53 19.33C13.25 18.44 11.48 16.14 11.37 15.99C11.26 15.84 10.4 14.7 10.4 13.52C10.4 12.34 11.01 11.75 11.26 11.5C11.45 11.31 11.86 11.21 12.19 11.21C12.33 11.21 12.46 11.22 12.57 11.22C12.89 11.24 13.06 11.26 13.28 11.78C13.56 12.45 14.24 14.11 14.32 14.28C14.4 14.45 14.48 14.69 14.37 14.92C14.26 15.15 14.17 15.26 14 15.45C13.83 15.64 13.68 15.78 13.52 15.97C13.38 16.14 13.21 16.33 13.39 16.65C13.57 16.97 14.2 18.02 15.15 18.87C16.38 19.96 17.39 20.31 17.76 20.46C18.13 20.61 18.35 20.57 18.55 20.34C18.75 20.11 19.42 19.32 19.63 19.01C19.84 18.7 20.05 18.76 20.36 18.87C20.67 18.98 22.35 19.81 22.69 19.98C23.03 20.15 23.26 20.23 23.34 20.37C23.42 20.51 23.42 21.23 23.12 21.79C22.72 22.56 21.36 23 20.4 23.1C19.2 23.2 18.5 23.1 17.21 22.5C14.9 21.5 13.3 19.4 13.2 19.2C13.1 19 12.3 17.9 12.3 16.82C12.3 15.74 12.86 15.2 13.09 14.97C13.26 14.8 13.63 14.71 13.93 14.71C14.06 14.71 14.18 14.72 14.28 14.72C14.57 14.74 14.72 14.76 14.92 15.23C15.17 15.84 15.79 17.36 15.86 17.51C15.93 17.66 16 17.88 15.9 18.09C15.8 18.3 15.72 18.4 15.57 18.57C15.42 18.74 15.29 18.87 15.14 19.04C15.01 19.19 14.85 19.36 15.02 19.65C15.19 19.94 15.76 20.9 16.63 21.68C17.76 22.68 18.69 23 19.03 23.14C19.37 23.28 19.57 23.24 19.75 23.03C19.93 22.82 20.54 22.1 20.73 21.82C20.92 21.54 21.11 21.59 21.39 21.69C21.67 21.79 23.21 22.55 23.52 22.71C23.83 22.87 24.04 22.95 24.11 23.08C24.18 23.21 24.18 23.87 23.9 24.38C23.53 25.09 22.29 25.5 21.4 25.59C20.3 25.68 19.65 25.68 17.9 25.13" fill="#25D366" />
                </svg>
              </div>
              <div className="whatsapp-pill-text-col">
                <span className="wa-title">WhatsApp</span>
                <span className="wa-sub">Click to Chat</span>
              </div>
            </a>
          </div>
        </div>

        {/* Banner 2: Call Back Advisor */}
        <div className="call-advisor-banner">
          {/* Circular phone icon */}
          <div className="banner-icon-wrapper">
            <div className="call-icon-circle">
              <PhoneCall size={22} className="phone-icon-svg" />
            </div>
          </div>

          {/* Middle text */}
          <div className="banner-text-wrapper">
            <h4 className="call-banner-title">Unable to Find the right test ?</h4>
            <p className="call-banner-sub">
              Need Help With Home Collection Booking? Get A Call Back From Our Health Advisor
            </p>
            <span className="call-banner-hours">7:00 AM - 11:00 PM</span>
          </div>

          {/* Right Button */}
          <div className="banner-btn-wrapper">
            <a href={`tel:${SUPPORT_PHONE}`} className="btn-call-now">
              CALL NOW
            </a>
          </div>
        </div>

      </div>
    </section>
  );
};

export default ContactBanners;
