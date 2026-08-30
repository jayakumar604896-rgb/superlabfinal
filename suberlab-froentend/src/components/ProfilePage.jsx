import { useState, useEffect } from 'react';
import {
  User, Mail, Phone, Clock, Download, ArrowLeft, Shield, MapPin,
  Activity, Award, UserCheck, AlertCircle, Pencil, X
} from 'lucide-react';
import { getSuperlabCustomer, setSuperlabCustomer, logoutSuperlabCustomer, fetchCustomerProfileData, updateCustomerProfile } from '../services/api';

const ProfilePage = () => {
  const [savedCustomer, setSavedCustomer] = useState(() => getSuperlabCustomer());
  const [liveCustomer, setLiveCustomer] = useState(null);
  const [liveVitals, setLiveVitals] = useState([]);
  const [liveBookings, setLiveBookings] = useState([]);
  const [isLoadingProfile, setIsLoadingProfile] = useState(false);
  const [isEditOpen, setIsEditOpen] = useState(false);
  const [isSavingProfile, setIsSavingProfile] = useState(false);
  const [profileMessage, setProfileMessage] = useState(null);
  const [editForm, setEditForm] = useState({
    name: '',
    email: '',
    age: '',
    gender: 'Male',
    blood_group: '',
    address: '',
    emergency_contact_name: '',
    emergency_contact_phone: '',
  });

  const syncCustomerToStorage = (customer) => {
    const current = getSuperlabCustomer();
    if (!current?.token || !customer) return;
    setSuperlabCustomer({
      ...current,
      id: customer.id,
      name: customer.name,
      email: customer.email,
      mobile: customer.mobile,
      age: customer.age,
      gender: customer.gender,
      blood_group: customer.blood_group,
      address: customer.address,
      emergency_contact_name: customer.emergency_contact_name,
      emergency_contact_phone: customer.emergency_contact_phone,
    });
  };

  const openEditForm = () => {
    const source = liveCustomer || savedCustomer || {};
    setEditForm({
      name: source.name || '',
      email: source.email || '',
      age: source.age ?? '',
      gender: source.gender || 'Male',
      blood_group: source.blood_group || '',
      address: source.address || '',
      emergency_contact_name: source.emergency_contact_name || '',
      emergency_contact_phone: source.emergency_contact_phone || '',
    });
    setProfileMessage(null);
    setIsEditOpen(true);
  };

  const handleSaveProfile = async (e) => {
    e.preventDefault();
    setIsSavingProfile(true);
    setProfileMessage(null);

    const payload = {
      name: editForm.name.trim(),
      email: editForm.email.trim() || null,
      age: editForm.age === '' ? null : Number(editForm.age),
      gender: editForm.gender || null,
      blood_group: editForm.blood_group.trim() || null,
      address: editForm.address.trim() || null,
      emergency_contact_name: editForm.emergency_contact_name.trim() || null,
      emergency_contact_phone: editForm.emergency_contact_phone.trim() || null,
    };

    const result = await updateCustomerProfile(payload);
    setIsSavingProfile(false);

    if (result.success && result.data) {
      setLiveCustomer(result.data);
      syncCustomerToStorage(result.data);
      setProfileMessage({ type: 'success', text: result.message || 'Profile updated successfully.' });
      setIsEditOpen(false);
    } else {
      setProfileMessage({ type: 'error', text: result.message || 'Failed to update profile.' });
    }
  };

  useEffect(() => {
    const handleAuthUpdate = () => {
      setSavedCustomer(getSuperlabCustomer());
    };
    window.addEventListener('superlab_auth_update', handleAuthUpdate);
    return () => window.removeEventListener('superlab_auth_update', handleAuthUpdate);
  }, []);

  useEffect(() => {
    if (savedCustomer?.token) {
      setIsLoadingProfile(true);
      fetchCustomerProfileData()
        .then(res => {
          if (res.success && res.data) {
            if (res.data.customer) {
              setLiveCustomer(res.data.customer);
              syncCustomerToStorage(res.data.customer);
            }
            if (res.data.vitals) setLiveVitals(res.data.vitals);
            if (res.data.bookings) setLiveBookings(res.data.bookings);
          } else if (res.message?.includes('Session expired')) {
            localStorage.removeItem('superlab_customer');
            window.dispatchEvent(new Event('superlab_auth_update'));
          }
        })
        .finally(() => setIsLoadingProfile(false));
    } else {
      setLiveCustomer(null);
      setLiveVitals([]);
      setLiveBookings([]);
    }
  }, [savedCustomer]);

  if (!savedCustomer?.token) {
    return (
      <div style={{ minHeight: '60vh', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '40px 20px' }}>
        <div style={{ textAlign: 'center', maxWidth: '420px' }}>
          <User size={48} style={{ color: '#00a3ad', marginBottom: '16px' }} />
          <h2 style={{ color: '#003c71', marginBottom: '8px' }}>Login Required</h2>
          <p style={{ color: '#64748b', marginBottom: '20px' }}>
            Please log in to view your profile, vitals, and booking history.
          </p>
          <a
            href="#/"
            onClick={(e) => {
              e.preventDefault();
              window.location.hash = '#/';
              window.dispatchEvent(new Event('superlab_open_login'));
            }}
            style={{
              display: 'inline-block',
              backgroundColor: '#00a3ad',
              color: '#fff',
              padding: '12px 24px',
              borderRadius: '8px',
              fontWeight: '700',
              textDecoration: 'none',
            }}
          >
            Go to Home & Login
          </a>
        </div>
      </div>
    );
  }

  const source = liveCustomer || savedCustomer || {};
  const patient = {
    id: source.id ? `SL-CUST-${source.id}` : 'SL-GUEST',
    name: source.name || 'Patient',
    email: source.email || 'Not Provided',
    phone: source.mobile
      ? (source.mobile.startsWith('+91') ? source.mobile : `+91 ${source.mobile}`)
      : '+91 N/A',
    age: source.age ?? 'N/A',
    gender: source.gender || 'N/A',
    bloodGroup: source.blood_group || 'Unknown',
    address: source.address || 'Not Provided',
    memberSince: source.member_since || 'N/A',
    emergencyContact: {
      name: source.emergency_contact_name || 'Not Provided',
      phone: source.emergency_contact_phone || 'Not Provided',
    },
  };

  const handleDownloadReport = (booking) => {
    if (booking.reportUrl) {
      window.open(booking.reportUrl, '_blank');
    } else {
      alert(`Report PDF for ${booking.id} is being processed by our pathologists.`);
    }
  };

  return (
    <div className="profile-page-container">
      <div className="profile-page-wrapper">
        
        {/* Back Link */}
        <div style={{ marginBottom: '24px' }}>
          <a href="#/" className="profile-back-link">
            <ArrowLeft size={16} />
            Back to Home
          </a>
        </div>

        {/* Profile Grid */}
        <div className="profile-grid-container">
          
          {/* Main Hero Header Card */}
          <div className="profile-hero-card">
            <div className="profile-avatar-wrapper">
              {/* User Avatar Circle */}
              <div className="profile-avatar-circle">
                <User size={40} style={{ color: '#ffffff' }} />
              </div>
              
              <div className="profile-user-info">
                <div className="profile-user-name-row">
                  <h1 className="profile-user-name">{patient.name}</h1>
                  <span className="profile-patient-id">
                    ID: {patient.id}
                  </span>
                </div>
                
                <p className="profile-member-since">
                  <UserCheck size={16} /> Member Since {patient.memberSince}
                </p>
              </div>
            </div>

            {/* Quick Details Badges */}
            <div className="profile-quick-details">
              <div className="profile-quick-badge">
                <span className="profile-quick-badge-label">Blood</span>
                <span className="profile-quick-badge-value orange">{patient.bloodGroup}</span>
              </div>
              <div className="profile-quick-badge">
                <span className="profile-quick-badge-label">Age</span>
                <span className="profile-quick-badge-value">{patient.age} Yrs</span>
              </div>
              <div className="profile-quick-badge">
                <span className="profile-quick-badge-label">Gender</span>
                <span className="profile-quick-badge-value">{patient.gender}</span>
              </div>
              {savedCustomer && (
                <>
                  <button
                    onClick={openEditForm}
                    style={{
                      backgroundColor: '#00a3ad',
                      color: '#ffffff',
                      border: 'none',
                      borderRadius: '8px',
                      padding: '8px 16px',
                      fontWeight: '700',
                      fontSize: '0.85rem',
                      cursor: 'pointer',
                      alignSelf: 'center',
                      marginLeft: 'auto',
                      display: 'inline-flex',
                      alignItems: 'center',
                      gap: '6px',
                    }}
                  >
                    <Pencil size={14} /> Edit Profile
                  </button>
                  <button 
                    onClick={() => logoutSuperlabCustomer()}
                    style={{
                      backgroundColor: '#ef4444',
                      color: '#ffffff',
                      border: 'none',
                      borderRadius: '8px',
                      padding: '8px 16px',
                      fontWeight: '700',
                      fontSize: '0.85rem',
                      cursor: 'pointer',
                      alignSelf: 'center',
                    }}
                  >
                    Logout
                  </button>
                </>
              )}
            </div>
          </div>

          {/* Two Columns for Info & History */}
          <div className="profile-columns-layout">
            
            {/* Left Column: Personal info & Health metrics */}
            <div className="profile-left-column">
              
              {/* Patient Contact & Emergency Info */}
              <div className="profile-card">
                <h3 className="profile-card-title-border">
                  Contact Information
                </h3>
                
                <div className="profile-info-list">
                  <div className="profile-info-item">
                    <div className="profile-info-icon-wrapper">
                      <Phone size={16} />
                    </div>
                    <div>
                      <span className="profile-info-label">Phone Number</span>
                      <span className="profile-info-value">{patient.phone}</span>
                    </div>
                  </div>

                  <div className="profile-info-item">
                    <div className="profile-info-icon-wrapper">
                      <Mail size={16} />
                    </div>
                    <div>
                      <span className="profile-info-label">Email Address</span>
                      <span className="profile-info-value">{patient.email}</span>
                    </div>
                  </div>

                  <div className="profile-info-item">
                    <div className="profile-info-icon-wrapper">
                      <MapPin size={16} />
                    </div>
                    <div>
                      <span className="profile-info-label">Residential Address</span>
                      <span className="profile-info-value address">{patient.address}</span>
                    </div>
                  </div>

                  <div className="profile-info-item" style={{ borderTop: '1px solid #f1f5f9', paddingTop: '16px', marginTop: '4px' }}>
                    <div className="profile-info-icon-wrapper emergency">
                      <Shield size={16} />
                    </div>
                    <div>
                      <span className="profile-info-label">Emergency Contact</span>
                      <span className="profile-info-value">{patient?.emergencyContact?.name || 'N/A'}</span>
                      <span className="profile-info-value emergency-phone">{patient?.emergencyContact?.phone || 'N/A'}</span>
                    </div>
                  </div>
                </div>
              </div>

              {/* Health Metrics Card */}
              <div className="profile-card">
                <div className="profile-card-header">
                  <h3 className="profile-card-title">
                    Recent Lab Metrics
                  </h3>
                  <Activity size={18} style={{ color: 'var(--teal)' }} />
                </div>

                <div className="profile-metrics-list">
                  {liveVitals.length > 0 ? (
                    liveVitals.map((vit, idx) => (
                      <div key={idx} className="profile-metric-item" style={{ borderLeft: `4px solid ${vit.color || '#10b981'}` }}>
                        <div>
                          <span className="profile-metric-name">{vit.name}</span>
                          <span className="profile-metric-range">{vit.range ? `Normal: ${vit.range}` : 'Lab Tested'}</span>
                        </div>
                        <div style={{ textAlign: 'right' }}>
                          <span className="profile-metric-value">
                            {vit.value} <span className="profile-metric-unit">{vit.unit}</span>
                          </span>
                          <span className="profile-metric-status">
                            {vit.status}
                          </span>
                        </div>
                      </div>
                    ))
                  ) : (
                    <div style={{ padding: '20px', textAlign: 'center', color: '#64748b' }}>
                      <AlertCircle size={24} style={{ color: '#94a3b8', marginBottom: '8px' }} />
                      <p style={{ margin: 0, fontSize: '0.88rem', fontWeight: '500' }}>
                        No lab test metrics recorded yet. Metrics will appear here after sample testing.
                      </p>
                    </div>
                  )}
                </div>
              </div>
              
            </div>

            {/* Right Column: Bookings and Dealings History */}
            <div className="profile-card" style={{ display: 'flex', flexDirection: 'column' }}>
              <div className="profile-card-header">
                <h3 className="profile-card-title">
                  Previous Dealings & Lab History
                </h3>
                <Award size={20} style={{ color: 'var(--orange)' }} />
              </div>

              <div className="profile-bookings-list">
                {liveBookings.length > 0 ? (
                  liveBookings.map((booking, idx) => (
                    <div key={idx} className="profile-booking-card">
                      {/* Header Row */}
                      <div className="profile-booking-header">
                        <div>
                          <span className="profile-booking-id">ORDER ID: {booking.id}</span>
                          <h4 className="profile-booking-name">{booking.testName}</h4>
                        </div>
                        <span className="profile-booking-status" style={{ backgroundColor: booking.badgeColor || '#f59e0b' }}>
                          {booking.status}
                        </span>
                      </div>

                      <p className="profile-booking-desc">
                        {booking.description}
                      </p>

                      {/* Footer Row */}
                      <div className="profile-booking-footer">
                        <div className="profile-booking-meta-group">
                          <div>
                            <span className="profile-booking-meta-label">DATE</span>
                            <span className="profile-booking-meta-value">{booking.date}</span>
                          </div>
                          <div>
                            <span className="profile-booking-meta-label">AMOUNT PAID</span>
                            <span className="profile-booking-meta-value">{booking.amount}</span>
                          </div>
                        </div>

                        {booking.canDownload && booking.reportUrl ? (
                          <button 
                            onClick={() => handleDownloadReport(booking)}
                            className="btn-profile-download"
                          >
                            <Download size={14} />
                            Download Report
                          </button>
                        ) : (
                          <span className="profile-awaiting-badge">
                            <Clock size={14} />
                            {booking.status === 'Completed' ? 'Report Ready' : 'Awaiting Sample'}
                          </span>
                        )}
                      </div>
                    </div>
                  ))
                ) : (
                  <div style={{ padding: '36px 20px', textAlign: 'center', color: '#64748b' }}>
                    <Clock size={32} style={{ color: '#94a3b8', marginBottom: '12px' }} />
                    <h4 style={{ margin: '0 0 6px 0', fontSize: '1rem', color: '#334155' }}>No Booking History Found</h4>
                    <p style={{ margin: '0 0 16px 0', fontSize: '0.85rem' }}>
                      You haven't placed any lab test bookings yet. Book a health checkup to view your orders here.
                    </p>
                    <a 
                      href="#/lab-tests" 
                      style={{
                        display: 'inline-block',
                        backgroundColor: 'var(--teal)',
                        color: '#ffffff',
                        padding: '10px 20px',
                        borderRadius: '8px',
                        fontWeight: '700',
                        fontSize: '0.88rem',
                        textDecoration: 'none'
                      }}
                    >
                      Book a Test Now
                    </a>
                  </div>
                )}
              </div>
            </div>

          </div>

        </div>

        {profileMessage && !isEditOpen && (
          <div style={{
            marginBottom: '16px',
            padding: '12px 16px',
            borderRadius: '8px',
            backgroundColor: profileMessage.type === 'success' ? '#ecfdf5' : '#fef2f2',
            color: profileMessage.type === 'success' ? '#047857' : '#b91c1c',
            fontWeight: '600',
            fontSize: '0.9rem',
          }}>
            {profileMessage.text}
          </div>
        )}

        {isLoadingProfile && (
          <p style={{ color: '#64748b', marginBottom: '16px', fontSize: '0.9rem' }}>Loading profile...</p>
        )}

        {isEditOpen && (
          <div style={{
            position: 'fixed',
            inset: 0,
            backgroundColor: 'rgba(15, 23, 42, 0.55)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            zIndex: 1000,
            padding: '20px',
          }}>
            <div style={{
              backgroundColor: '#fff',
              borderRadius: '16px',
              width: '100%',
              maxWidth: '560px',
              maxHeight: '90vh',
              overflowY: 'auto',
              boxShadow: '0 20px 40px rgba(0,0,0,0.15)',
            }}>
              <div style={{
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
                padding: '20px 24px',
                borderBottom: '1px solid #e2e8f0',
              }}>
                <h2 style={{ margin: 0, fontSize: '1.2rem', color: '#003c71' }}>Edit Profile</h2>
                <button
                  type="button"
                  onClick={() => setIsEditOpen(false)}
                  style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#64748b' }}
                  aria-label="Close"
                >
                  <X size={20} />
                </button>
              </div>

              <form onSubmit={handleSaveProfile} style={{ padding: '24px' }}>
                {profileMessage?.type === 'error' && (
                  <div style={{
                    marginBottom: '16px',
                    padding: '10px 12px',
                    borderRadius: '8px',
                    backgroundColor: '#fef2f2',
                    color: '#b91c1c',
                    fontSize: '0.88rem',
                  }}>
                    {profileMessage.text}
                  </div>
                )}

                <div style={{ display: 'grid', gap: '14px' }}>
                  <label style={{ display: 'grid', gap: '6px' }}>
                    <span style={{ fontSize: '0.85rem', fontWeight: '700', color: '#475569' }}>Full Name *</span>
                    <input required className="form-input" value={editForm.name} onChange={(e) => setEditForm({ ...editForm, name: e.target.value })} />
                  </label>

                  <label style={{ display: 'grid', gap: '6px' }}>
                    <span style={{ fontSize: '0.85rem', fontWeight: '700', color: '#475569' }}>Mobile (read-only)</span>
                    <input disabled className="form-input" value={source.mobile || ''} />
                  </label>

                  <label style={{ display: 'grid', gap: '6px' }}>
                    <span style={{ fontSize: '0.85rem', fontWeight: '700', color: '#475569' }}>Email</span>
                    <input type="email" className="form-input" value={editForm.email} onChange={(e) => setEditForm({ ...editForm, email: e.target.value })} />
                  </label>

                  <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px' }}>
                    <label style={{ display: 'grid', gap: '6px' }}>
                      <span style={{ fontSize: '0.85rem', fontWeight: '700', color: '#475569' }}>Age</span>
                      <input type="number" min="1" max="120" className="form-input" value={editForm.age} onChange={(e) => setEditForm({ ...editForm, age: e.target.value })} />
                    </label>
                    <label style={{ display: 'grid', gap: '6px' }}>
                      <span style={{ fontSize: '0.85rem', fontWeight: '700', color: '#475569' }}>Gender</span>
                      <select className="form-input" value={editForm.gender} onChange={(e) => setEditForm({ ...editForm, gender: e.target.value })}>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                      </select>
                    </label>
                  </div>

                  <label style={{ display: 'grid', gap: '6px' }}>
                    <span style={{ fontSize: '0.85rem', fontWeight: '700', color: '#475569' }}>Blood Group</span>
                    <input className="form-input" placeholder="e.g. O+" value={editForm.blood_group} onChange={(e) => setEditForm({ ...editForm, blood_group: e.target.value })} />
                  </label>

                  <label style={{ display: 'grid', gap: '6px' }}>
                    <span style={{ fontSize: '0.85rem', fontWeight: '700', color: '#475569' }}>Address</span>
                    <textarea className="form-input" rows={3} value={editForm.address} onChange={(e) => setEditForm({ ...editForm, address: e.target.value })} />
                  </label>

                  <label style={{ display: 'grid', gap: '6px' }}>
                    <span style={{ fontSize: '0.85rem', fontWeight: '700', color: '#475569' }}>Emergency Contact Name</span>
                    <input className="form-input" value={editForm.emergency_contact_name} onChange={(e) => setEditForm({ ...editForm, emergency_contact_name: e.target.value })} />
                  </label>

                  <label style={{ display: 'grid', gap: '6px' }}>
                    <span style={{ fontSize: '0.85rem', fontWeight: '700', color: '#475569' }}>Emergency Contact Phone</span>
                    <input className="form-input" value={editForm.emergency_contact_phone} onChange={(e) => setEditForm({ ...editForm, emergency_contact_phone: e.target.value })} />
                  </label>
                </div>

                <div style={{ display: 'flex', gap: '12px', marginTop: '20px' }}>
                  <button
                    type="submit"
                    disabled={isSavingProfile}
                    style={{
                      flex: 1,
                      backgroundColor: '#00a3ad',
                      color: '#fff',
                      border: 'none',
                      borderRadius: '8px',
                      padding: '12px',
                      fontWeight: '700',
                      cursor: isSavingProfile ? 'not-allowed' : 'pointer',
                      opacity: isSavingProfile ? 0.7 : 1,
                    }}
                  >
                    {isSavingProfile ? 'Saving...' : 'Save Changes'}
                  </button>
                  <button
                    type="button"
                    onClick={() => setIsEditOpen(false)}
                    style={{
                      flex: 1,
                      backgroundColor: '#f1f5f9',
                      color: '#334155',
                      border: 'none',
                      borderRadius: '8px',
                      padding: '12px',
                      fontWeight: '700',
                      cursor: 'pointer',
                    }}
                  >
                    Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
        )}

      </div>
    </div>
  );
};

export default ProfilePage;
