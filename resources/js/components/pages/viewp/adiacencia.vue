<template>
    <v-card>
        <v-card-text>


            <vue-excel-editor
               v-model="jsondata"
                width="100%"
                readonly
                disable-panel-filter
                disable-panel-setting
                ref="grid"
            >
            <vue-excel-column field="node" label="Node" type="string" readonly width="200px"/>
            <vue-excel-column field="relaciones" label="Relations" type="string" width="500px" />
            </vue-excel-editor>
        </v-card-text>
        {{ $store.state.selectGraph.adjacencyList }}
    </v-card>

   
</template>
<script>
export default {
    name: "Adiacencia",
    props: ["headers", "datos"],
    components: {},
    data: () => ({
        jsondata: [],
        encabezados: []
    }),

    mounted() {},
    created() {},
    beforeMount() {},
    watch: {
        "$store.state.selectGraph.adjacencyList"(){
             // Transformar los datos originales al formato adecuado para vue-excel-editor
            //this.jsondata = this.$store.state.selectGraph.adjacencyList;
            this.jsondata = [];
            console.log("INICIE");
            console.log(this.$store.state.selectGraph.adjacencyList.length);
            console.log(this.$store.state.singleTable);
            for(var i=0; this.$store.state.selectGraph.adjacencyList.length>i; i++){
                console.log(this.$store.state.singleTable[i]);
                var titulo = this.$store.state.singleTable[i].NodeID + "-" + this.$store.state.singleTable[i].NodeName;
                this.encabezados.push(titulo);
                console.log(this.$store.state.selectGraph.adjacencyList[i]);
                var elementos = "";
                for(var u=0; this.$store.state.selectGraph.adjacencyList[i].length>u; u++){
                    console.log(this.$store.state.selectGraph.adjacencyList[i][u][0]);
                    const index = this.$store.state.singleTable.findIndex(item => item.NodeID == this.$store.state.selectGraph.adjacencyList[i][u][0]+1);
                    console.log(index);
                    if(index == -1){
                        console.log("NO EXISTE");
                    } else {
                        console.log(this.$store.state.singleTable[index]);
                        
                        elementos = elementos + this.$store.state.singleTable[index].NodeID + "-" + this.$store.state.singleTable[index].NodeName + "; ";
                        
                        
                        
                        
                    }
                }

                var elemento = { node: this.$store.state.singleTable[i].NodeID + "-" + this.$store.state.singleTable[i].NodeName, 
                        relaciones: elementos };
                        console.log(elemento);
                        this.jsondata.push(elemento);
                
            }

            console.log(this.encabezados);
            console.log(this.jsondata);
            
        }
    },
    computed: {
        formattedData() {
           
    }
    },
    methods: {}
};
</script>
