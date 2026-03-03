import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { getMisProyectos, cancelarProyecto, completarProyecto } from '../services/projectService';

export default function MyProjects() {
  const navigate = useNavigate();
  const [proyectos, setProyectos] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  useEffect(() => {
    fetchProyectos();
  }, []);

  const fetchProyectos = async () => {
    try {
      setLoading(true);
      const response = await getMisProyectos();
      // Filtra solo los proyectos del usuario 1
      const allProjects = Array.isArray(response.data.data) ? response.data.data : response.data.items || response.data;
      const filtered = Array.isArray(allProjects) ? allProjects.filter(p => p.user_id === 1) : [];
      setProyectos(filtered);
    } catch (err) {
      setError('Error al cargar los proyectos');
      console.error('Error:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleCancelar = async (id) => {
    if (window.confirm('¿Está seguro que desea cancelar este proyecto?')) {
      try {
        await cancelarProyecto(id);
        setSuccess('Proyecto cancelado correctamente');
        setTimeout(() => setSuccess(''), 3000);
        fetchProyectos();
      } catch (err) {
        setError(err.response?.data?.error || 'Error al cancelar el proyecto');
        setTimeout(() => setError(''), 3000);
        console.error('Error:', err);
      }
    }
  };

  const handleCompletar = async (id) => {
    if (window.confirm('¿Está seguro que desea completar este proyecto?')) {
      try {
        await completarProyecto(id);
        setSuccess('Proyecto completado correctamente');
        setTimeout(() => setSuccess(''), 3000);
        fetchProyectos();
      } catch (err) {
        setError(err.response?.data?.error || 'Error al completar el proyecto');
        setTimeout(() => setError(''), 3000);
        console.error('Error:', err);
      }
    }
  };

  if (loading) return <div className="loading">Cargando proyectos...</div>;

  return (
    <div className="container">
      <div className="flex-between" style={{ marginBottom: '30px' }}>
        <h1>Mis Proyectos</h1>
        <button 
          onClick={() => navigate('/crear-proyecto')}
          className="btn-primary"
        >
          + Crear Proyecto
        </button>
      </div>

      {error && <div className="alert alert-error">{error}</div>}
      {success && <div className="alert alert-success">{success}</div>}

      {proyectos.length === 0 ? (
        <div className="card" style={{ textAlign: 'center', padding: '60px 20px' }}>
          <p>No tienes proyectos aún</p>
          <button 
            onClick={() => navigate('/crear-proyecto')}
            className="btn-primary"
          >
            Crear tu primer proyecto
          </button>
        </div>
      ) : (
        <div className="grid">
          {proyectos.map(proyecto => (
            <div key={proyecto.id} className="project-card">
              <img src={proyecto.imagen_url} alt={proyecto.nombre} />
              <div className="project-card-content">
                <h3>{proyecto.nombre}</h3>
                <p style={{ color: '#666', fontSize: '14px', marginBottom: '10px' }}>{proyecto.descripcion.substring(0, 80)}...</p>
                <div style={{ marginBottom: '10px' }}>
                  <span className={`badge badge-${proyecto.estado === 'activo' ? 'active' : proyecto.estado === 'pendiente' ? 'pending' : proyecto.estado === 'completado' ? 'completed' : 'cancelled'}`}>
                    {proyecto.estado}
                  </span>
                </div>
                <small style={{ color: '#999' }}>Inversión: ${proyecto.inversion_actual} / ${proyecto.max_inversion}</small>
                <div className="flex" style={{ marginTop: '15px', flexWrap: 'wrap' }}>
                  <button 
                    onClick={() => navigate(`/proyecto/${proyecto.id}`)}
                    className="btn-secondary"
                    style={{ flex: 1 }}
                  >
                    Ver
                  </button>
                  {proyecto.estado === 'pendiente' || proyecto.estado === 'activo' ? (
                    <>
                      <button 
                        onClick={() => handleCancelar(proyecto.id)}
                        className="btn-danger"
                        style={{ flex: 1 }}
                      >
                        Cancelar
                      </button>
                      {proyecto.estado === 'activo' && proyecto.inversion_actual >= proyecto.min_inversion && (
                        <button 
                          onClick={() => handleCompletar(proyecto.id)}
                          className="btn-success"
                          style={{ flex: 1 }}
                        >
                          Completar
                        </button>
                      )}
                    </>
                  ) : null}
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
