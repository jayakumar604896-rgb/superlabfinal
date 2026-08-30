import { Microscope, Hospital, ClipboardCheck, ChevronRight } from 'lucide-react';

const openReportModal = (e) => {
  e.preventDefault();
  window.dispatchEvent(new Event('superlab_open_report_modal'));
};

const QuickActions = () => {
  const actions = [
    {
      title: 'Book a Lab Test',
      subtext: 'Home Sample Collection',
      icon: <Microscope size={36} className="action-icon" />,
      colorClass: 'card-teal',
      link: '#find-test'
    },
    {
      title: 'Book Full Body Packages',
      subtext: 'Health Checkup Packages',
      icon: <Hospital size={36} className="action-icon" />,
      colorClass: 'card-blue',
      link: '#health-checkup'
    },
    {
      title: 'Download Reports',
      subtext: 'Check E-Reports Status',
      icon: <ClipboardCheck size={36} className="action-icon" />,
      colorClass: 'card-pale-green',
      onClick: openReportModal,
    }
  ];

  return (
    <section className="quick-actions-section">
      <div className="quick-actions-container">
        <div className="quick-actions-grid">
          {actions.map((act, index) => (
            act.onClick ? (
              <button
                key={index}
                type="button"
                onClick={act.onClick}
                className={`action-card ${act.colorClass}`}
                style={{ border: 'none', width: '100%', textAlign: 'left', cursor: 'pointer' }}
              >
                <div className="action-card-left">
                  <div className="icon-wrapper">{act.icon}</div>
                  <div className="text-wrapper">
                    <h4 className="action-title">{act.title}</h4>
                    <p className="action-subtext">{act.subtext}</p>
                  </div>
                </div>
                <div className="action-arrow-btn">
                  <ChevronRight size={18} className="arrow-icon" />
                </div>
              </button>
            ) : (
              <a key={index} href={act.link} className={`action-card ${act.colorClass}`}>
                <div className="action-card-left">
                  <div className="icon-wrapper">{act.icon}</div>
                  <div className="text-wrapper">
                    <h4 className="action-title">{act.title}</h4>
                    <p className="action-subtext">{act.subtext}</p>
                  </div>
                </div>
                <div className="action-arrow-btn">
                  <ChevronRight size={18} className="arrow-icon" />
                </div>
              </a>
            )
          ))}
        </div>
      </div>
    </section>
  );
};

export default QuickActions;
