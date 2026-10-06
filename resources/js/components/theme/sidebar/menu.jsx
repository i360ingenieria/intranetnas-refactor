const menu = [
  {
    path: "/",
    icon: "nav-icon fas fa-tachometer-alt",
    title: "Dashboard"
  },
  {
    path: "/",
    icon: "nav-icon fas fa-database",
    title: "Docuemntos Nas",
    children: [
      {
        path: "/explorer",
        title: "Gestion De Calidad "
      },
      {
        path: "/fichatecnica",
        title: "Ficha  Tecnica"
      }
    ]
  },
  {
    path: "/",
    icon: "nav-icon fas fa-database",
    title: "INFORMACION",
    children: [
      {
        path: "/",
        title: "Directorio"
      },
      {
        path: "",
        title: "Avisos",
        icon: "nav-icon fas far fa-circle nav-icon",
        children: [
          {
            path: "/",
            title: "Sub Level 2"
          },
          {
            path: "/",
            title: "Sub Level 3"
          },
          {
            path: "/",
            title: "Sub Level 4"
          }
        ]
      },
      {
        path: "",
        title: "Level 2",
        icon: "nav-icon fas far fa-circle nav-icon",
        children: [
          {
            path: "/",
            title: "Sub Level 2"
          },
          {
            path: "/",
            title: "Sub Level 3"
          },
          {
            path: "/",
            title: "Sub Level 4"
          }
        ]
      },
      {
        path: "",
        title: "Level 2",
        icon: "nav-icon fas far fa-circle nav-icon",
        children: [
          {
            path: "/",
            title: "Sub Level 2"
          },
          {
            path: "/",
            title: "Sub Level 3"
          },
          {
            path: "/",
            title: "Sub Level 4"
          }
        ]
      },
      {
        path: "/",
        title: "Level 3"
      }
    ]
  }
];

export default menu;
