<template>
    <div>

        <h4>INFORMATION</h4>
        

        <p> <b>Nombre del proyecto:</b> {{ info.cName }}<br>
            <b>Descripción:</b> {{ info.cDescription }}<br>
            <b>Creado el:</b> {{ info.created_at }} <br>
            <b>Ultima Modificación:</b> {{ info.updated_at }} <br>
        </p>

        

    </div>

</template>
<script>

export default {
    name: "",
    props: ["info"],
    components: {

    },
    data: () => ({

    }),
    mounted() {

    },
    created() {

    },
    beforeMount() {

    },
    watch: {

    },
    computed: {

    },
    methods: {

    }
}
</script>