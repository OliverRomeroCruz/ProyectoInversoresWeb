import { useEffect, useState } from 'react'
import { getProjectsList } from '../services/projectService'
import ProjectCard from '../components/ProjectCard'

function ProjectList() {
    const [project, setProject] = useState([])
    const [page, setPage] = useState(1)
    const [hasNext, setHasNext] = useState(false)

    useEffect(() => {
        getProjectsList(page)
            .then((response) => {
                // Filtra solo proyectos activos y completados
                const filtered = response.data.data.filter(p => p.estado === 'activo' || p.estado === 'completado')
                setProject(filtered)
                setHasNext(response.data.pagination.has_next_page)
            })
            .catch((error) => console.error(error))
    }, [page])

    return (
        <div className="container">
            <h1>Proyectos Disponibles</h1>
            <div className="grid">
                {project.map((project) => (
                    <ProjectCard key={project.id} project={project} />
                ))}
            </div>
        </div>
    )
}

export default ProjectList