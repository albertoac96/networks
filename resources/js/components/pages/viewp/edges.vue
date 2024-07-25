<template>
    <v-card>
        <v-card-text>
            <button @click="exportAsExcel">Export Excel</button>

            <vue-excel-editor
                v-model="this.$store.state.formatedData.edges"
                width="100%"
                readonly
                disable-panel-filter
                disable-panel-setting
                ref="grid"
            >
                <vue-excel-column field="node_id" label="Node ID" />
                <vue-excel-column field="node_name" label="Node Name" />
                <vue-excel-column field="edge_id" label="Edge ID"/>
                <vue-excel-column field="edge_name" label="Edge Name" />
            </vue-excel-editor>
        </v-card-text>
    </v-card>
</template>
<script>
import JsonExcel from "vue-json-excel";
export default {
    name: "",
    props: ["datos"],
    components: {},
    data: () => ({
        headers: [],
        data: []
    }),
    mounted() {
        this.inicio();
    },
    created() {},
    beforeMount() {},
    watch: {
        "$store.state.selectGraph"() {
            console.log("entra a ver el select graph");
           
        }
    },
    computed: {},
    methods: {
        inicio() {
            //console.log(this.datos.length);
           
        },
        exportAsExcel() {
            const format = "xlsx";
            const exportSelectedOnly = false;
            const filename = "test";
            this.$refs.grid.exportTable(format, exportSelectedOnly, filename);
        },
        
    }
};
</script>