import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { createProject } from '../services/projectService';

export default function CreateProject() {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    nombre: '',
    descripcion: '',
    imagen_url: '',
    video_url: '',
    min_inversion: '',
    max_inversion: '',
    fecha_fin: '',
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    try {
      const dataToSend = {
        ...formData,
        min_inversion: parseFloat(formData.min_inversion),
        max_inversion: parseFloat(formData.max_inversion),
        estado: 'pendiente',
        inversion_actual: 0
      };

      await createProject(dataToSend);
      navigate('/mis-proyectos');
    } catch (err) {
      setError(err.response?.data?.message || 'Error al crear el proyecto');
      console.error('Error:', err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container" style={{ maxWidth: '600px' }}>
      <h1>Crear Nuevo Proyecto</h1>
      
      {error && <div className="alert alert-error">{error}</div>}

      <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
        <div>
          <label>Nombre *</label>
          <input
            type="text"
            name="nombre"
            value={formData.nombre}
            onChange={handleChange}
            required
          />
        </div>

        <div>
          <label>Descripción *</label>
          <textarea
            name="descripcion"
            value={formData.descripcion}
            onChange={handleChange}
            required
            rows={5}
          />
        </div>

        <div>
          <label>URL Imagen *</label>
          <input
            type="url"
            name="imagen_url"
            value={formData.imagen_url}
            onChange={handleChange}
            required
          />
        </div>

        <div>
          <label>URL Video</label>
          <input
            type="url"
            name="video_url"
            value={formData.video_url}
            onChange={handleChange}
          />
        </div>

        <div>
          <label>Inversión Mínima *</label>
          <input
            type="number"
            name="min_inversion"
            value={formData.min_inversion}
            onChange={handleChange}
            required
            min={0}
            step={0.01}
          />
        </div>

        <div>
          <label>Inversión Máxima *</label>
          <input
            type="number"
            name="max_inversion"
            value={formData.max_inversion}
            onChange={handleChange}
            required
            min={0}
            step={0.01}
          />
        </div>

        <div>
          <label>Fecha de Finalización *</label>
          <input
            type="date"
            name="fecha_fin"
            value={formData.fecha_fin}
            onChange={handleChange}
            required
          />
        </div>

        <div style={{ display: 'flex', gap: '10px' }}>
          <button type="submit" disabled={loading} className="btn-primary" style={{ flex: 1 }}>
            {loading ? 'Creando...' : 'Crear Proyecto'}
          </button>
          <button
            type="button"
            onClick={() => navigate('/mis-proyectos')}
            className="btn-cancel"
            style={{ flex: 1 }}
          >
            Cancelar
          </button>
        </div>
      </form>
    </div>
  );
}
