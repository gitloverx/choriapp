import { useState, useEffect } from 'react';
import { api } from '../api';
export default function Dashboard({ businessName, onLogout }) {
  const [products, setProducts] = useState([]);
  useEffect(() => { api('/api/products').then(setProducts).catch(console.error); }, []);
  return (
    <div style={{ padding: 20, maxWidth: 800, margin: '0 auto' }}>
      <div style={{ display: 'flex', justifyContent: 'space-between' }}>
        <h2>🌭 {businessName}</h2>
        <button onClick={onLogout} style={{ padding: '5px 10px' }}>Salir</button>
      </div>
      <h3>Productos en Supabase:</h3>
      <ul>
        {products.map(p => (
          <li key={p.id}><strong>{p.name}</strong> - ${p.price.toLocaleString()}</li>
        ))}
      </ul>
    </div>
  );
}
