
  export default {
    created(){
      //console.log(Permissions.indexOf('listadocs'));
    },
    methods: {
      $can(permissionName) {
        
        return Permissions.indexOf(permissionName) !== -1;
        
      },
    },
  };

