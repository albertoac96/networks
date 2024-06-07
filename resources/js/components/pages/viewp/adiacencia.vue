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
                    v-for="item in headers"
                    :key="item.index"
                    :field="item.index"
                    :label="item.index"
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
            this.jsondata = this.$store.state.selectGraph.adjacencyList;
            return originalData.map(row => {
                const formattedRow = {};
                row.forEach((item, index) => {
                formattedRow[`col${index + 1}`] = `(${item[0]}, ${item[1]})`;
                });
                return formattedRow;
            });
        }
    },
    computed: {
        formattedData() {
     
    }
    },
    methods: {}
};
</script>
