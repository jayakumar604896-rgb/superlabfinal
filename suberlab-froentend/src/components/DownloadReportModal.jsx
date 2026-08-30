import { useEffect, useState } from 'react';
import { X, FileText } from 'lucide-react';
import { lookupGuestReport } from '../services/api';
import { saveGuestReportSession } from './ReportDownloadPage';

const DownloadReportModal = ({ isOpen, onClose }) => {
  const [mobile, setMobile] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (isOpen) {
      setMobile('');
      setError('');
      setLoading(false);
    }
  }, [isOpen]);

  if (!isOpen) return null;

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    const digits = mobile.replace(/\D/g, '');
    if (digits.length !== 10) {
      setError('Enter a valid 10-digit mobile number.');
      return;
    }

    setLoading(true);
    const result = await lookupGuestReport(digits);
    setLoading(false);

    if (result.success) {
      saveGuestReportSession(result.data);
      onClose();
      window.location.hash = '#/download-report';
      return;
    }

    setError(result.message || 'No report found for this mobile number.');
  };

  return (
    <div className="login-modal-backdrop" onClick={onClose}>
      <div className="login-modal-card" onClick={(e) => e.stopPropagation()} style={{ maxWidth: '420px' }}>
        <div className="login-modal-header">
          <h3 className="login-modal-title">Download Report</h3>
          <button type="button" className="login-modal-close-btn" onClick={onClose}>
            <X size={20} />
          </button>
        </div>

        <form onSubmit={handleSubmit} style={{ padding: '0 4px 8px' }}>
          <p className="login-modal-subtitle" style={{ marginBottom: '16px' }}>
            Enter the mobile number used when booking. We will show your latest available report.
          </p>

          <label htmlFor="report-mobile" style={{ display: 'block', fontWeight: 700, fontSize: '0.85rem', color: 'var(--blue)', marginBottom: '6px' }}>
            Mobile Number
          </label>
          <input
            id="report-mobile"
            type="tel"
            inputMode="numeric"
            maxLength={10}
            value={mobile}
            onChange={(e) => setMobile(e.target.value.replace(/\D/g, '').slice(0, 10))}
            placeholder="10-digit mobile number"
            style={{
              border: '1.5px solid #cbd5e1',
              borderRadius: '8px',
              padding: '12px 14px',
              width: '100%',
              boxSizing: 'border-box',
              fontWeight: 600,
              fontSize: '0.95rem',
              marginBottom: '12px',
            }}
            autoFocus
          />

          {error && (
            <div style={{ color: '#dc2626', fontSize: '0.85rem', fontWeight: 600, marginBottom: '12px' }}>{error}</div>
          )}

          <button
            type="submit"
            disabled={loading}
            style={{
              width: '100%',
              backgroundColor: 'var(--teal)',
              color: '#fff',
              border: 'none',
              borderRadius: '10px',
              padding: '12px 16px',
              fontWeight: 800,
              fontSize: '0.95rem',
              cursor: loading ? 'wait' : 'pointer',
              opacity: loading ? 0.75 : 1,
              display: 'inline-flex',
              alignItems: 'center',
              justifyContent: 'center',
              gap: '8px',
            }}
          >
            <FileText size={18} />
            {loading ? 'Checking...' : 'Download Report'}
          </button>
        </form>
      </div>
    </div>
  );
};

export default DownloadReportModal;
