import { reactive, ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import cloneDeep from 'lodash/cloneDeep'

export function useList(readMethod)
{
    const pagination = ref(null);
    const sortFields = ref([]);
    const locked = ref(false);
    const storageKey = ref(null);

    const readCommonParams = reactive({
        page: 1,
        search: '',
        orderBy: 'created_at',
        orderDir: 'desc',
        limit: 10,
    });

    const filters = reactive({});

    const hasFilters = computed(() => {
        let hasFilters = (readCommonParams.search !== '');
        if(filters.length > 0){
            let idx = findIndex(filters, function(filter){
                return (filter.value !== null);
            });
            hasFilters |= (idx !== -1);
        }
        return hasFilters;
    });

    const tableIsEmpty = computed(() => {
        return (pagination.value !== null && pagination.value.data.length === 0 && !hasFilters);
    });

    function saveToStorage(){
        if(storageKey.value){
            //console.log('saveToStorage', storageKey.value);
            localStorage.setItem(storageKey.value, JSON.stringify({
                filters: filters,
                readCommonParams: readCommonParams,
            }));
        }
    }

    function loadFromStorage(){
        let data = {};
        if(storageKey.value){
            if(localStorage.getItem(storageKey.value) !== null){
                data = JSON.parse(localStorage.getItem(storageKey.value));
                //console.log('loadFromStorage', storageKey.value, data);
            }
        }
        return data;
    }

    function initCommonParamsFromStorage(){
        if(storageKey.value)
        {
            let data = loadFromStorage();
            //let params = cloneDeep(readCommonParams.value);
            
            if(data.readCommonParams)
            {
                // Lock watchers and assign (thus unwatched) values
                locked.value = true;
                readCommonParams.page = parseInt(data.readCommonParams.page ?? readCommonParams.page);
                readCommonParams.limit = parseInt(data.readCommonParams.limit ?? readCommonParams.limit);
                readCommonParams.search = data.readCommonParams.search ?? readCommonParams.search;
                readCommonParams.orderBy = data.readCommonParams.orderBy ?? readCommonParams.orderBy;
                readCommonParams.orderDir = data.readCommonParams.orderDir ?? readCommonParams.orderDir;
                locked.value = false;
            }
        }
    }

    function initFiltersFromStorage(){
        if(storageKey.value)
        {
            let data = loadFromStorage();
            let filters = cloneDeep(filters);

            if(data.filters)
            {
                // This method should be overwritten in child components 
                filters = assignStoredFilters(filters, data.filters);

                // Lock watchers and assign (thus unwatched) values
                locked.value = true;
                filters = filters;
                locked.value = false;
            }
        }
    }

    function assignStoredFilters(targetFilters, storedFilters){
        // This is the opportunity to assign filters retreived from local storage to the local "filters".
        // You can manipulate storedFilters the way you want to assign targetFilters properties
        return Object.assign({}, targetFilters, storedFilters);
    }

    function readPage1(){
        locked.value = true;
        readCommonParams.page = 1;
        locked.value = false;
        read();
    }

    function read(){
        if(readMethod){
            readMethod();
        }else{
            throw "The read() method must be given by the component using useList()"
        }
    }

    function readParams(){
        return {
            page: readCommonParams.page,
            search: readCommonParams.search,
            order_by: readCommonParams.orderBy,
            order_dir: readCommonParams.orderDir,
            limit: readCommonParams.limit,
        };
    }

    watch(() => readCommonParams.page, (newValue, oldValue) => {
        if(!locked.value){
            read();
        }
    })

    watch(() => readCommonParams.search, (newValue, oldValue) => {
        if(!locked.value){
            readPage1();
        }
    })

    watch(() => readCommonParams.orderBy, (newValue, oldValue) => {
        if(!locked.value){
            readPage1();
        }
    })

    watch(() => readCommonParams.orderDir, (newValue, oldValue) => {
        if(!locked.value){
            readPage1();
        }
    })

    watch(() => readCommonParams.limit, (newValue, oldValue) => {
        if(!locked.value){
            readPage1();
        }
    })

    watch(readCommonParams, (newValue, oldValue) =>
    {
        saveToStorage();
    });

    watch(filters, (newFilters, oldFilters) =>
    {
        if(!locked.value)
        {
            // Lock so the watcher is not triggered by the page change
            locked.value = true;
            readCommonParams.page = 1;
            locked.value = false;

            saveToStorage();
            read();
        }
    });

    onMounted(() => {
        initCommonParamsFromStorage();
        initFiltersFromStorage();
    });

    onBeforeUnmount(() => {
        if(storageKey.value){
            saveToStorage();    
        }
    });

    return {
        pagination,
        sortFields,
        filters,
        locked,
        storageKey,
        readCommonParams,

        hasFilters,
        tableIsEmpty,
        storageKey,

        saveToStorage,
        loadFromStorage,
        initCommonParamsFromStorage,
        initFiltersFromStorage,
        assignStoredFilters,

        read,
        readPage1,
        readParams,
    };
}
