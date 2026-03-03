import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { getProjectById } from '../services/projectService';

function ProjectDetail() {
  const { id } = useParams(); // Tomamos el id de la URL
  const [project, setProject] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    getProjectById(id)
      .then((response) => {
        setProject(response.data); // Aquí asumimos que la API devuelve el proyecto completo
        setLoading(false);
      })
      .catch((err) => {
        setError('No se pudo cargar el proyecto.');
        setLoading(false);
      });
  }, [id]);

  if (loading) return <div className="loading">Cargando...</div>;
  if (error) return <div className="container"><div className="alert alert-error">{error}</div></div>;
  if (!project) return <div className="container"><div className="alert alert-error">Proyecto no encontrado.</div></div>;

  return (
    <div className="container">
      <div className="card">
        {project.imagen_url && <img src={project.imagen_url} alt={project.nombre} style={{ width: '100%', maxHeight: '400px', objectFit: 'cover', borderRadius: '6px', marginBottom: '20px' }} />}
        <h1>{project.nombre}</h1>
        <p style={{ fontSize: '16px', lineHeight: '1.6', color: '#555', marginBottom: '20px' }}>{project.descripcion}</p>
        {project.estado && (
          <div style={{ marginBottom: '20px' }}>
            <span className={`badge badge-${project.estado === 'activo' ? 'active' : project.estado === 'pendiente' ? 'pending' : project.estado === 'completado' ? 'completed' : 'cancelled'}`}>
              {project.estado}
            </span>
          </div>
        )}
        {project.inversion_actual !== undefined && project.max_inversion !== undefined && (
          <p style={{ fontSize: '14px', color: '#666' }}>
            <strong>Inversión:</strong> ${project.inversion_actual} / ${project.max_inversion}
          </p>
        )}
        {project.video_url && (
          <div style={{ marginTop: '20px' }}>
            <h3>Video</h3>
            <a href={project.video_url} target="_blank" rel="noopener noreferrer" className="btn-primary">
              Ver Video
            </a>
          </div>
        )}
      </div>
    </div>
  );
}

export default ProjectDetail;
