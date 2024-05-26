<template>
    <v-card>
        <v-card-text>
            <button @click="exportAsExcel">Export Excel</button>

            <vue-excel-editor
                v-model="$store.state.selectGraph.EdgesList"
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
    </v-card>
</template>
<script>
import JsonExcel from "vue-json-excel";
export default {
    name: "",
    props: ["datos"],
    components: {},
    data: () => ({
        headers: []
    }),
    mounted() {
        this.inicio();
    },
    created() {},
    beforeMount() {},
    watch: {
        "this.$store.state.selectGraph"() {
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
        }
    }
};
</script>