import { Link } from 'react-router-dom'

function Navbar() {
    return (
        <nav>
            <Link to="/">Proyectos</Link>
            <Link to="/mis-proyectos">Mis Proyectos</Link>
            <Link to="/crear-proyecto">Crear Proyecto</Link>
        </nav>
    )
}

export default Navbar
