import { API } from './api';
import { ID_USER } from '../constants';

// ============= FUNCIONES BÁSICAS DE PROYECTOS =============

// Obtener lista de proyectos con paginación
export function getProjectsList(page = 1) {
  return API.get(`/proyectos?page=${page}`);
}

// Obtener proyecto por id
export function getProjectById(id) {
  return API.get(`/proyectos/${id}`);
}

// Crear un proyecto
export function createProject(data) {
  const projectData = {
    user_id: ID_USER,
    ...data
  };
  return API.post('/proyectos', projectData);
}

// Actualizar un proyecto
export function updateProject(id, data) {
  return API.put(`/proyectos/${id}`, data);
}

// Eliminar un proyecto
export function deleteProject(id) {
  return API.delete(`/proyectos/${id}`);
}

// ============= FUNCIONES PARA EMPRENDEDORES =============

// Obtener mis proyectos personales
export function getMisProyectos() {
  return API.get(`/proyectos?user_id=${ID_USER}`);
}

// Cancelar un proyecto
export function cancelarProyecto(id) {
  return API.put(`/proyectos/${id}/cancelar`);
}

// Completar un proyecto
export function completarProyecto(id) {
  return API.put(`/proyectos/${id}/completar`);
}

// Editar un proyecto
export function editarProyecto(id, data) {
  return API.put(`/proyectos/${id}`, data);
}

// ============= FUNCIONES DE INVERSIÓN =============

// Realizar una inversión en un proyecto
export function invertir(id, monto) {
  return API.post(`/proyectos/${id}/invertir`, { monto });
}

// Retirar una inversión
export function retirarInversion(id) {
  return API.post(`/proyecto/${id}/retirar`);
}

// Obtener mis inversiones personales
export function getMisInversiones() {
  return API.get(`/mis-inversiones/${ID_USER}`);
}
