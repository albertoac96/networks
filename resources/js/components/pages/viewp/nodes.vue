<template>
    <v-card>
        <v-card-text>
            <button @click="exportAsExcel">Export Excel</button>

            <p>Mean Control: {{ $store.state.meanControl }}</p>

            <vue-excel-editor
                v-model="$store.state.singleTable"
                width="100%"
                readonly
                disable-panel-filter
                disable-panel-setting
                ref="grid"
            >
            <vue-excel-column field="NodeID" label="ID" type="string"  />
            <vue-excel-column field="NodeName" label="NodeName" type="string"  />
            <vue-excel-column field="NodeX" label="NodeX" type="number"  />
            <vue-excel-column field="NodeY" label="NodeY" type="number"  />
            <vue-excel-column field="ControlValue" label="Control Value" type="number"  />
            <vue-excel-column field="RelativeAssymetry" label="Relative Assymetry" type="number"  />
  
            </vue-excel-editor>
        </v-card-text>
    </v-card>
</template>
<script>
import JsonExcel from "vue-json-excel";
export default {
    name: "",
    props: ["headers", "datos", "singleTable"],
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