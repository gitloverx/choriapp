import { useEffect, useState } from 'react';
import UserLogin from './pages/UserLogin';
import Dashboard from './pages/Dashboard';

export default function App() {
  const [user, setUser] = useState(null);
  useEffect(() => {
    const token = localStorage.getItem('token');
    const name = localStorage.getItem('businessName');
    if (token && name) setUser(name);
  }, []);
  const handleLogout = () => { localStorage.clear(); setUser(null); };
  return user ? <Dashboard businessName={user} onLogout={handleLogout} /> : <UserLogin setUser={setUser} />;
}
