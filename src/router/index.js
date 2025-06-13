import Vue from 'vue'
import Router from 'vue-router'
import HelloWorld from '@/components/HelloWorld'
import Insumos from '../components/Insumos/Insumos'
import Categorias from '../components/Categorias/Categorias'
import RegistrarInsumo from '../components/Insumos/RegistrarInsumo'
import EditarInsumo from '../components/Insumos/EditarInsumo'
import Configurar from '../components/Configuracion/Configurar'
import RealizarOrden from '../components/Ordenar/RealizarOrden'
import Ordenar from '../components/Ordenar/Ordenar'
import Usuarios from '../components/Usuarios/Usuarios'
import RegistrarUsuario from '../components/Usuarios/RegistrarUsuario'
import EditarUsuario from '../components/Usuarios/EditarUsuario'
import Login from '../components/Usuarios/Login'
import Perfil from '../components/Usuarios/Perfil'
import CambiarPassword from '../components/Usuarios/CambiarPassword'
import Inicio from '../components/Inicio'
import ReporteVentas from '../components/Ventas/ReporteVentas'

Vue.use(Router)

const router = new Router({
  routes: [
    {
      path: '/',
      name: 'Inicio',
      component: Inicio,
      meta: { roles: ['admin'] }
    },
    {
      path: '/insumos',
      name: 'Insumos',
      component: Insumos,
      meta: { roles: ['admin', 'mesero', 'cajero'] }
    },
    {
      path: '/configurar',
      name: 'Configurar',
      component: Configurar,
      meta: { roles: ['admin'] }
    },
    {
      path: '/realizar-orden',
      name: 'RealizarOrden',
      component: RealizarOrden,
      meta: { roles: ['admin', 'mesero', 'cajero'] }
    },
    {
      path: '/ordenar/:id',
      name: 'Ordenar',
      component: Ordenar,
      props: true,
      meta: { roles: ['admin', 'mesero', 'cajero'] }
    },
    {
      path: '/registrar-insumo',
      name: 'RegistrarInsumo',
      component: RegistrarInsumo,
      meta: { roles: ['admin'] }
    },
    {
      path: '/categorias',
      name: 'Categorias',
      component: Categorias,
      meta: { roles: ['admin', 'mesero', 'cajero'] }
    },
    {
      path: '/editar-insumo/:id',
      name: 'EditarInsumo',
      component: EditarInsumo,
      meta: { roles: ['admin'] }
    },
    {
      path: '/usuarios',
      name: 'Usuarios',
      component: Usuarios,
      meta: { roles: ['admin'] }
    },
    {
      path: '/perfil',
      name: 'Perfil',
      component: Perfil,
      meta: { roles: ['admin', 'mesero', 'cajero'] }
    },
    {
      path: '/cambiar-password',
      name: 'CambiarPassword',
      component: CambiarPassword,
      meta: { roles: ['admin', 'mesero', 'cajero'] }
    },
    {
      path: '/registrar-usuario',
      name: 'RegistrarUsuario',
      component: RegistrarUsuario,
      meta: { roles: ['admin'] }
    },
    {
      path: '/editar-usuario/:id',
      name: 'EditarUsuario',
      component: EditarUsuario,
      meta: { roles: ['admin'] }
    },
    {
      path: '/reporte-ventas',
      name: 'ReporteVentas',
      component: ReporteVentas,
      meta: { roles: ['admin'] }
    },
  ]
})

// Guard de roles
router.beforeEach((to, from, next) => {
  let rol = (localStorage.getItem('rol') || '').toLowerCase();
  if (rol === 'administrador') rol = 'admin';
  if (!rol) {
    // Si no hay rol, redirige a perfil o login
    return next('/perfil');
  }
  if (to.meta && to.meta.roles && !to.meta.roles.includes(rol)) {
    if (rol !== 'admin') {
      return next('/perfil');
    }
    return next('/');
  }
  next();
});

export default router;