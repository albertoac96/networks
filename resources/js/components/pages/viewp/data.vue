<template>

    <v-card>
    
        <v-card-text>
            <template>
    <vue-excel-editor v-model="datos" width="100%" readonly>
        <vue-excel-column v-for="(item, index) in headers" :key="index" :field="index" :label="item" type="string"  />
    </vue-excel-editor>
</template>

        </v-card-text>

</v-card>
    
    </template>
    <script>



    export default {
        name: "",
        props: ["headers", "datos", "singleTable"],
        components:{
           
        },
        data: () => ({
            jsondata: [
            {nodeid: '1', name: 'Acatlan',    x: '-98.66972222', y: '19.915'},
            {nodeid: '23', name: 'Acelotla',    x: '-98.63353927', y: '19.99641388'},
            {nodeid: '464', name: 'San Lorenzo',    x: '-98.62313909', y: '20.08636'},
            {nodeid: '2', name: 'Actopan',    x: '-98.7321', y: '19.915'},
            {nodeid: '67', name: 'Epazoyucan',    x: '-98.75', y: '20.00765743'},
            {nodeid: '32', name: 'Huejutla',    x: '-98.5517', y: '19.16'},
        ],
        }),
       
       mounted(){
            
        },
        created(){
            
        },
        beforeMount(){
    
        },
        watch:{
            
        },
        computed:{
       
        },
        methods:{
           
        }
    }
    </script>