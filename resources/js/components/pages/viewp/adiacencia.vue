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
                <vue-excel-column
                    v-for="(item, index) in Object.keys(jsondata[0])"
                    :key="index"
                    :field="item"
                    :label="item"
                    type="string"
                />
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
        jsondata: []
    }),

    mounted() {},
    created() {},
    beforeMount() {},
    watch: {
        "$store.state.selectGraph.adjacencyList"(){
             // Transformar los datos originales al formato adecuado para vue-excel-editor
            //this.jsondata = this.$store.state.selectGraph.adjacencyList;
            this.jsondata = [];
            this.formattedData();
            
        }
    },
    computed: {
        formattedData() {
            this.jsondata = this.$store.state.selectGraph.adjacencyList.map((row, rowIndex) => {
                const obj = {};
                row.forEach(([key, value], index) => {
                    obj[`column_${index}`] = value;
                });
                return obj;
            });
         
    }
    },
    methods: {}
};
</script>
