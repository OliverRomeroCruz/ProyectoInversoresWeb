import { Link } from 'react-router-dom';

function ProjectCard({ project }) {
  const { id, nombre, descripcion, imagen_url, estado, inversion_actual, max_inversion } = project;

  return (
    <Link to={`/proyecto/${id}`} style={{ textDecoration: 'none', color: 'inherit' }}>
      <div className="project-card">
        {imagen_url && <img src={imagen_url} alt={nombre} />}
        <div className="project-card-content">
          <h3>{nombre}</h3>
          <p style={{ color: '#666', fontSize: '14px', marginBottom: '10px' }}>{descripcion?.substring(0, 100)}...</p>
          {estado && (
            <div style={{ marginBottom: '10px' }}>
              <span className={`badge badge-${estado === 'activo' ? 'active' : estado === 'pendiente' ? 'pending' : estado === 'completado' ? 'completed' : 'cancelled'}`}>
                {estado}
              </span>
            </div>
          )}
          {inversion_actual !== undefined && max_inversion !== undefined && (
            <small style={{ color: '#999' }}>
              Inversión: ${inversion_actual} / ${max_inversion}
            </small>
          )}
        </div>
      </div>
    </Link>
  );
}

export default ProjectCard;
