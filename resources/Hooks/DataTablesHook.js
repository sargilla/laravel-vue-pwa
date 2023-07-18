import { ref } from "vue";
import { api } from "@/boot/axios";
import { dataTablesQuery } from "./DataTablesQueryGenerator";
export const DataTablesHook = ({
    sortBy = "",
    url = "",
    page = 1,
    rowsPerPage = 10,
    fetchData,
    columns = [],
    descending = false,
}) => {
    const filter = ref("");
    const loading = ref(false);
    const paginationSettings = ref({
        sortBy,
        descending,
        page,
        rowsNumber: 0,
        rowsPerPage,
    });
    async function onRequest({
        pagination = paginationSettings.value,
        filter = "",
        fetch = null,
    }) {
        const { page, rowsPerPage, sortBy, descending } = pagination;
        loading.value = true;
        const params = dataTablesQuery({
            columns,
            pagination,
            filter,
        });
        url = fetch ?? url;
        const { data } = await api.get(url, { params });
        fetchData.value = data.data;
        paginationSettings.value.rowsNumber = data?.recordsFiltered;
        paginationSettings.value.page = page;
        paginationSettings.value.rowsPerPage = rowsPerPage;
        paginationSettings.value.sortBy = sortBy;
        paginationSettings.value.descending = descending;
        paginationSettings.value.url = url;
        loading.value = false;
    }
    return { filter, loading, paginationSettings, onRequest };
};
