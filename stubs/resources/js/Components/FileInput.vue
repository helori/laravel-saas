<template>
    <span class="document-input">

        <input 
            ref="fileInput"
            type="file" 
            :multiple="multiple"
            :id="'file-input-' + id"
            class="file-input absolute"
            style="left: -9999px"
            @change="setFiles">

        <label 
            :for="'file-input-' + id" 
            class="filedrop m-0"
            :class="classes">
            <slot name="content"></slot>
        </label>

    </span>
</template>

<script>

import { defineComponent, ref } from 'vue'

export default defineComponent(
{
    props: {
        multiple: {
            type: Boolean,
            required: false,
            default: false
        },
        inputText: {
            type: String,
            required: false,
            default: 'Browse / Drop files'
        },
        classes: {
            type: String,
            required: false,
            default: 'btn btn-primary'
        }
    },

    setup(props, { emit })
    {
        const fileInput = ref(null);
        const id = ref(Math.floor(Math.random()*(9999-1000+1)+1000));

        // -------------------------------------------------------
        //  Init browse button
        // -------------------------------------------------------
        function setFiles(e)
        {
            emit('files-selected', {
                files: e.target.files, 
                multiple: props.multiple,
            });
        }

        return {
            id,
            fileInput,
            setFiles,
        };
    },
})
</script>
