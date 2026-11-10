const API_URL = import.meta.env.VITE_API_URL || `http://${window.location.hostname}:8000`;
export async function api(path, { method = 'GET', body, auth = true } = {}) {
  const headers = { 'Content-Type': 'application/json' };
  if (auth) {
    const token = localStorage.getItem('token');
    if (token) headers.Authorization = `Bearer ${token}`;
  }
  const res = await fetch(`${API_URL}${path}`, { method, headers, body: body ? JSON.stringify(body) : undefined });
  let data = null; try { data = await res.json(); } catch { data = {}; }
  if (!res.ok) throw new Error(data?.error || `Error ${res.status}`);
  return data;
}
