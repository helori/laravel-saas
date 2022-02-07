import { ref } from 'vue'

export function useForm()
{
    const data = ref({});
    const params = ref({});
    const promise = ref(null);
    const status = ref(null);
    const error = ref(null);

    function send(method, url, config)
    {
        status.value = 'pending';
        error.value = null;

        let requestConfig = {
            method: method,
            url: url,
            data: data.value,
            params: params.value,
        };

        if(config){
            requestConfig = {...requestConfig, ...config};
        }
        
        return promise.value = axios.request(requestConfig).then(r => {

            status.value = 'success';
            error.value = null;
            
            // Needed to call then(r) again on the returned promise
            return r;

        }).catch(r => {

            status.value = 'error';
            error.value = r;

            throw r;
        });
    }

    return {
        data,
        params,
        promise,
        status,
        error,
        send,
    };
}
