import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import ProjectList from './pages/ProjectList';
import ProjectDetail from './pages/ProjectDetail';
import CreateProject from './pages/CreateProject';
import MyProjects from './pages/MyProjects';
import Navbar from './components/Navbar';

function App() {
  return (
    <Router>
      <Navbar />
      <Routes>
        <Route path="/" element={<ProjectList />} />
        <Route path="/proyecto/:id" element={<ProjectDetail />} />
        <Route path="/crear-proyecto" element={<CreateProject />} />
        <Route path="/mis-proyectos" element={<MyProjects />} />
      </Routes>
    </Router>
  );
}

export default App;
