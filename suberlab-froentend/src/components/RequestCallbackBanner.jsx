

import { useState } from 'react';
import { submitEnquiry } from '../services/api';

const RequestCallbackBanner = () => {
  const [submitting, setSubmitting] = useState(false);

  const handleCallbackRequest = async () => {
    const phone = prompt('Please enter your mobile number for a callback:');
    if (!phone) return;

    setSubmitting(true);
    const result = await submitEnquiry({
      name: 'Callback Request User',
      mobile: phone,
      message: `User requested callback for phone: ${phone}`,
      subject: `Callback Request - ${phone}`
    });
    setSubmitting(false);

    if (result.success) {
      alert(result.message || 'Callback requested successfully!');
    } else {
      alert(result.message || 'Failed to submit callback request. Please try again.');
    }
  };

  return (
    <section className="request-callback-section">
      <div className="request-callback-container">
        
        {/* Left Side: Overlapping Call Operator image */}
        <div className="callback-image-wrapper">
          <img 
            src="/call_operator.png" 
            alt="Customer Support Operator" 
            className="callback-operator-img"
          />
        </div>

        {/* Middle: Content details */}
        <div className="callback-content-wrapper">
          <h4 className="callback-title">Unable to Find the right test ?</h4>
          <span className="callback-hours">7:00 AM - 11:00 PM</span>
        </div>

        {/* Right Side: Button */}
        <div className="callback-btn-wrapper">
          <button 
            className="btn-request-callback" 
            onClick={handleCallbackRequest}
            disabled={submitting}
          >
            {submitting ? 'Submitting...' : 'Request A Call Back'}
          </button>
        </div>

      </div>
    </section>
  );
};

export default RequestCallbackBanner;
