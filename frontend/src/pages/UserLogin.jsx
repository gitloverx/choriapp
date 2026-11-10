import { useState } from 'react';
import { api } from '../api';
export default function UserLogin({ setUser }) {
  const [email, setEmail] = useState('netflis7575@gmail.com');
  const [password, setPassword] = useState('12345678');
  const [error, setError] = useState('');
  async function submit(e) {
    e.preventDefault(); setError('');
    try {
      const r = await api('/api/login', { method: 'POST', auth: false, body: { email, password } });
      localStorage.setItem('token', r.token);
      localStorage.setItem('businessName', r.business_name || 'Mi Negocio');
      setUser(r.business_name || 'Mi Negocio');
    } catch (err) { setError(err.message); }
  }
  return (
    <div style={{ padding: 20, maxWidth: 400, margin: '50px auto', background: '#fff', borderRadius: 8 }}>
      <h2>🌭 ChoriApp Login</h2>
      <input type="email" value={email} onChange={e => setEmail(e.target.value)} style={{ display:'block', width:'100%', padding: 10, marginBottom: 10 }} />
      <input type="password" value={password} onChange={e => setPassword(e.target.value)} style={{ display:'block', width:'100%', padding: 10, marginBottom: 10 }} />
      {error && <p style={{ color: 'red' }}>{error}</p>}
      <button onClick={submit} style={{ padding: 10, width: '100%', background: '#e0a51e', border: 'none', color: '#fff' }}>Entrar</button>
    </div>
  );
}
