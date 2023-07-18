export const dataTablesQuery = ({ columns, pagination, filter = '' }) => {
    // console.log(filter);
    const { page, rowsPerPage } = pagination;
    const index = columns.findIndex((item) => item?.name === pagination?.sortBy);
    const orderBy = index > 0 ? index : 0;
    const direction = pagination.descending ? 'desc' : 'asc';

    const convertdColumns = columns
        .filter((entrie) => !entrie?.noDataField)
        .map((entrie, i) => [
            [`columns[${i}][data]`, entrie?.field],
            [`columns[${i}][name]`, entrie?.name ?? ''],
            [`columns[${i}][orderable]`, entrie?.sortable ?? false],
            [`columns[${i}][searchable]`, entrie?.searchable ?? true],
        ])
        .flat();
    const queries = new URLSearchParams(convertdColumns);
    queries.append('order[0][column]', orderBy);
    queries.append('order[0][dir]', direction);
    queries.append('start', (page - 1) * rowsPerPage);
    queries.append('length', rowsPerPage ? rowsPerPage : -1);
    queries.append('search[value]', filter);
    return queries;
};
