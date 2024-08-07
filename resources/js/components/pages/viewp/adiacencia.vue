<template>
    <v-card>
        <v-card-text>
           

            <vue-excel-editor
                v-model="$store.state.formatedData.adjacencyList"
                :columns="$store.state.headers.adjacencyList"
                :cell-class="cellClass"
                width="100%"
                readonly
                disable-panel-filter
                disable-panel-setting
                ref="grid"
                @select="onSelect"
            >
               
            </vue-excel-editor>
        </v-card-text>
      
    </v-card>
</template>
<script>
export default {
    name: "Adiacencia",
    props: ["headers", "datos"],
    components: {},
    data: () => ({
        jsondata: [],
        encabezados: [],
        maxRelaciones: 0
    }),

    mounted() {},
    created() {},
    beforeMount() {},
    watch: {
        "$store.state.selectGraph.adjacencyList"() {
         
           
            
        }
    },
    computed: {
       
            preparedDataForExcel() {
               
            }
       
    },
    methods: {
       

        cellClass({ row, column }) {
      if (column === 'Node') {
        return 'bold-column';
      }
      return '';
    },
    onSelect (selectedRows) {
      console.log(selectedRows)
      const index = selectedRows[0];
      const obj = this.jsondata[index];

      const result = [];
        // Extraer el número del Node
        const nodeNumber = obj.Node.split('-')[0];
        result.push(nodeNumber);

        // Extraer los números de las relaciones
        Object.keys(obj).forEach(key => {
            if (key.startsWith('Relation') && obj[key]) {
            const relationNumber = obj[key].split('-')[0];
            result.push(relationNumber);
            }
        });
        console.log(result);

        var seleccionados =  result.map((result, index) => {
            const node = this.$store.state.singleTable.find(node => node.NodeID === result);
            if (node) {
                const color = index === 0 ? 'red' : 'yellow';
                return { ...node, color: color };
            }
        }).filter(node => node !== undefined); // Filtrar los nodos que no se encontraron

        console.log(seleccionados);
        this.$store.state.selectedItems = seleccionados;
    }

    }
};
</script>

<style scoped>
.bold-column {
  font-weight: bold;
}
</style>
