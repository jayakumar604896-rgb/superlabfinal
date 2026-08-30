import { useEffect, useState } from 'react';
import { Download, FileText, ArrowLeft } from 'lucide-react';

const STORAGE_KEY = 'superlab_guest_report';

export const saveGuestReportSession = (report) => {
  sessionStorage.setItem(STORAGE_KEY, JSON.stringify(report));
};

export const readGuestReportSession = () => {
  try {
    const raw = sessionStorage.getItem(STORAGE_KEY);
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
};

export const clearGuestReportSession = () => {
  sessionStorage.removeItem(STORAGE_KEY);
};

const ReportDownloadPage = () => {
  const [report, setReport] = useState(null);

  useEffect(() => {
    setReport(readGuestReportSession());
  }, []);

  if (!report) {
    return (
      <div style={{ minHeight: '60vh', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '40px 20px' }}>
        <div style={{ textAlign: 'center', maxWidth: '420px' }}>
          <FileText size={48} color="#94a3b8" style={{ marginBottom: '16px' }} />
          <h2 style={{ color: 'var(--blue)', marginBottom: '8px' }}>No report loaded</h2>
          <p style={{ color: 'var(--muted)', marginBottom: '20px' }}>
            Use Download Report in the menu and enter your registered mobile number.
          </p>
          <a href="#/" style={{ color: 'var(--teal)', fontWeight: 700, textDecoration: 'none' }}>Back to home</a>
        </div>
      </div>
    );
  }

  const isPdf = (report.file_name || report.report_url || '').toLowerCase().includes('.pdf');

  return (
    <div style={{ backgroundColor: '#f8fafc', minHeight: 'calc(100vh - 120px)', padding: '32px 20px 60px' }}>
      <div style={{ maxWidth: '960px', margin: '0 auto' }}>
        <a href="#/" style={{ display: 'inline-flex', alignItems: 'center', gap: '6px', color: 'var(--teal)', fontWeight: 700, textDecoration: 'none', marginBottom: '20px' }}>
          <ArrowLeft size={16} /> Back to home
        </a>

        <div style={{ background: '#fff', borderRadius: '16px', border: '1px solid var(--line)', boxShadow: 'var(--shadow-sm)', overflow: 'hidden' }}>
          <div style={{ padding: '24px', borderBottom: '1px solid var(--line)', display: 'flex', flexWrap: 'wrap', justifyContent: 'space-between', gap: '16px', alignItems: 'center' }}>
            <div>
              <h1 style={{ margin: 0, fontSize: '1.35rem', color: 'var(--blue)' }}>Your Lab Report</h1>
              <p style={{ margin: '6px 0 0', color: 'var(--muted)', fontSize: '0.92rem' }}>
                {report.patient_name} · {report.booking_number}
                {report.booking_date ? ` · ${report.booking_date}` : ''}
              </p>
            </div>
            <a
              href={report.report_url}
              download={report.file_name || 'lab-report.pdf'}
              target="_blank"
              rel="noopener noreferrer"
              style={{
                display: 'inline-flex',
                alignItems: 'center',
                gap: '8px',
                backgroundColor: 'var(--teal)',
                color: '#fff',
                padding: '12px 20px',
                borderRadius: '10px',
                fontWeight: 700,
                textDecoration: 'none',
              }}
            >
              <Download size={18} /> Download Report
            </a>
          </div>

          <div style={{ padding: '16px', background: '#f1f5f9', minHeight: '480px' }}>
            {isPdf ? (
              <iframe
                title="Lab report preview"
                src={report.report_url}
                style={{ width: '100%', minHeight: '70vh', border: 'none', borderRadius: '8px', background: '#fff' }}
              />
            ) : (
              <div style={{ textAlign: 'center', padding: '40px 20px' }}>
                <img
                  src={report.report_url}
                  alt="Lab report"
                  style={{ maxWidth: '100%', borderRadius: '8px', boxShadow: '0 4px 20px rgba(0,0,0,0.08)' }}
                />
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default ReportDownloadPage;
