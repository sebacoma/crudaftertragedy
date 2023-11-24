import React, { useState } from 'react';

const Login = () => {
  const [name, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const handleLogin = async () => {
    setIsLoading(true);
    setError('');

    try {
      const response = await fetch('http://192.168.1.88:8000/api/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name, password }),
      });

      if (!response.ok) {
        throw new Error('Credenciales inválidas');
      }

      // Aquí puedes manejar la lógica de éxito en el inicio de sesión,
      // como almacenar el token de autenticación en el estado o en localStorage.

      // Por ejemplo, si la API devuelve un token:
      const data = await response.json();
      console.log('Token de acceso:', data.token);

    } catch (error) {
      setError(error.message);
    }

    setIsLoading(false);
  };

  return (
    <div>
      <h2>Iniciar sesión</h2>
      {error && <p>{error}</p>}
      <form onSubmit={(e) => {
        e.preventDefault();
        handleLogin();
      }}>
        <label>
          Usuario:
          <input
            type="text"
            value={name}
            onChange={(e) => setUsername(e.target.value)}
          />
        </label>
        <br />
        <label>
          Contraseña:
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />
        </label>
        <br />
        <button type="submit" disabled={isLoading}>
          {isLoading ? 'Cargando...' : 'Iniciar sesión'}
        </button>
      </form>
    </div>
  );
};

export default Login;
