import axios, { errorMessage } from "@/axios.js";
import i18n from "@/i18n/i18n";

const defaultError = () => i18n.global.t("report_error");


// The report list rarely changes: fetch it once per session and share it between the list and the viewer.
let typesRequest = null;
export function fetchReportTypes() {
    if (!typesRequest) {
        typesRequest = axios.get("/api/v1/report/types")
            .then((response) => response.data || [])
            .catch((error) => {
                typesRequest = null; // allow a retry
                throw error;
            });
    }
    return typesRequest;
}

// Recently loaded report pages, so paging back and reopening a report show at once (then refresh).
const PAGE_CACHE_SIZE = 50;
const pageCache = new Map(); // key -> page data, oldest first
const pending = new Map();   // key -> request in flight, shared by prefetch and the viewer
const pageKey = (type, filters, page, limit) => JSON.stringify([type, filters, page, limit]);

export function cachedReportPage(type, filters = [], page = 1, limit = 25) {
    return pageCache.get(pageKey(type, filters, page, limit)) || null;
}

// One page of a report as JSON for the on-screen table: { columns, rows, total, page, limit, summary }.
export function fetchReportPage(type, filters = [], page = 1, limit = 25) {
    const key = pageKey(type, filters, page, limit);
    if (!pending.has(key)) {
        const request = axios.post(`/api/v1/report/${type}/data`, { data: filters, page, limit })
            .then((response) => {
                pageCache.delete(key);
                pageCache.set(key, response.data);
                if (pageCache.size > PAGE_CACHE_SIZE) pageCache.delete(pageCache.keys().next().value);
                return response.data;
            })
            .catch((error) => {
                throw errorMessage(error, defaultError());
            })
            .finally(() => pending.delete(key));
        pending.set(key, request);
    }
    return pending.get(key);
}

// Load a page in the background (e.g. the next page, or a report the pointer is over)
export function prefetchReportPage(type, filters = [], page = 1, limit = 25) {
    if (!cachedReportPage(type, filters, page, limit)) fetchReportPage(type, filters, page, limit).catch(() => {});
}

// Fetch a report PDF. Resolves { blob, filename }; rejects with a readable message.
export async function fetchReport(type, filters = [], download = false) {
    try {
        const response = await axios.post(
            `/api/v1/report/${type}` + (download ? "?download=1" : ""),
            { data: filters },
            { responseType: "blob" }
        );
        const disposition = response.headers["content-disposition"] || "";
        const match = disposition.match(/filename="?([^";]+)"?/);
        return {
            blob: new Blob([response.data], { type: "application/pdf" }),
            filename: match ? match[1] : `${type}-report.pdf`,
        };
    } catch (error) {
        // The reason is read from the error body even for a blob download (see axios.js)
        throw errorMessage(error, defaultError());
    }
}

export function saveBlob(blob, filename) {
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
}

// Download a report as an Excel workbook (same content and layout as the PDF)
export async function downloadReportExcel(type, filters = []) {
    try {
        const response = await axios.post(`/api/v1/report/${type}/excel`, { data: filters }, { responseType: "blob" });
        const match = (response.headers["content-disposition"] || "").match(/filename="?([^";]+)"?/);
        saveBlob(new Blob([response.data], { type: response.headers["content-type"] }), match ? match[1] : `${type}-report.xlsx`);
    } catch (error) {
        // The reason is read from the error body even for a blob download (see axios.js)
        throw errorMessage(error, defaultError());
    }
}

export async function downloadReport(type, filters = []) {
    const { blob, filename } = await fetchReport(type, filters, true);
    saveBlob(blob, filename);
}
