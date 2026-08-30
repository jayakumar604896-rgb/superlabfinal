import { useEffect } from 'react';
import { ArrowLeft } from 'lucide-react';

const LEGAL_CONTENT = {
  privacy: {
    title: 'Privacy Policy',
    sections: [
      {
        heading: 'Information we collect',
        body: 'When you book a test, create an account, or download a report, we may collect your name, mobile number, email address, age, gender, address, booking details, and payment-related information required to complete your order.',
      },
      {
        heading: 'How we use your information',
        body: 'We use your information to process bookings, arrange sample collection, deliver laboratory reports, provide customer support, and improve our services. We may contact you regarding your booking status or report availability.',
      },
      {
        heading: 'Report and health data',
        body: 'Your test results and medical reports are stored securely and shared only with you through your registered account or verified mobile number lookup. Access to reports requires the contact details provided at the time of booking.',
      },
      {
        heading: 'Data sharing',
        body: 'We do not sell your personal information. Data may be shared with authorised laboratory partners, phlebotomy service providers, and payment processors strictly as needed to fulfil your booking.',
      },
      {
        heading: 'Data security',
        body: 'We apply reasonable administrative and technical safeguards to protect your information. No method of transmission over the internet is completely secure, and we encourage you to keep your login credentials confidential.',
      },
      {
        heading: 'Your choices',
        body: 'You may update profile information from your account page. For questions about your data or to request correction of inaccurate information, contact us using the details on our website.',
      },
      {
        heading: 'Updates',
        body: 'We may update this Privacy Policy from time to time. Continued use of Super Lab services after changes are posted constitutes acceptance of the updated policy.',
      },
    ],
  },
  terms: {
    title: 'Terms and Conditions',
    sections: [
      {
        heading: 'Use of services',
        body: 'Super Lab provides diagnostic test booking and home sample collection services. By using our website or placing a booking, you agree to these Terms and Conditions.',
      },
      {
        heading: 'Bookings and payments',
        body: 'Prices shown on the website are subject to change. A booking is confirmed once submitted through our checkout flow. Payment terms depend on the method selected at checkout, including online payment or book-now-pay-later options where available.',
      },
      {
        heading: 'Coupons and offers',
        body: 'Promotional codes are subject to eligibility rules, validity dates, usage limits, and minimum order requirements. Only one coupon may be applied per order unless otherwise stated.',
      },
      {
        heading: 'Sample collection',
        body: 'You agree to provide accurate patient details and be available at the scheduled time and address for home collection. Incorrect information may delay testing or report delivery.',
      },
      {
        heading: 'Reports',
        body: 'Reports are made available electronically after processing. Turnaround times are estimates and may vary by test type. Reports are for informational use and do not replace consultation with a qualified medical professional.',
      },
      {
        heading: 'Cancellations and refunds',
        body: 'Cancellation and refund eligibility depends on booking status and sample collection progress. Contact customer support for assistance with booking changes.',
      },
      {
        heading: 'Limitation of liability',
        body: 'Super Lab is not liable for delays caused by factors outside our reasonable control, including courier issues, sample quality affected by patient preparation, or third-party service interruptions.',
      },
      {
        heading: 'Contact',
        body: 'For support regarding bookings, reports, or these terms, reach us via the contact options listed on our website.',
      },
    ],
  },
};

const LegalPage = ({ page = 'privacy' }) => {
  const content = LEGAL_CONTENT[page] || LEGAL_CONTENT.privacy;

  useEffect(() => {
    window.document.title = `${content.title} | SuperLab`;
  }, [content.title]);

  return (
    <div style={{ backgroundColor: '#f8fafc', minHeight: 'calc(100vh - 120px)', padding: '32px 20px 60px' }}>
      <div style={{ maxWidth: '800px', margin: '0 auto' }}>
        <a href="#/" style={{ display: 'inline-flex', alignItems: 'center', gap: '6px', color: 'var(--teal)', fontWeight: 700, textDecoration: 'none', marginBottom: '20px' }}>
          <ArrowLeft size={16} /> Back to home
        </a>

        <article style={{ background: '#fff', borderRadius: '16px', border: '1px solid var(--line)', boxShadow: 'var(--shadow-sm)', padding: '32px 28px' }}>
          <h1 style={{ margin: '0 0 8px', fontSize: '1.75rem', color: 'var(--blue)' }}>{content.title}</h1>
          <p style={{ margin: '0 0 28px', color: 'var(--muted)', fontSize: '0.9rem' }}>Last updated: August 2026</p>

          {content.sections.map((section) => (
            <section key={section.heading} style={{ marginBottom: '24px' }}>
              <h2 style={{ margin: '0 0 8px', fontSize: '1.05rem', color: 'var(--blue)' }}>{section.heading}</h2>
              <p style={{ margin: 0, color: '#475569', lineHeight: 1.65, fontSize: '0.95rem' }}>{section.body}</p>
            </section>
          ))}
        </article>
      </div>
    </div>
  );
};

export default LegalPage;
