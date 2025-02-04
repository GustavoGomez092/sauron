<template>
  <div class="max-w-full mx-auto mt-[10px] mr-[20px] mb-0 ml-[2px]">
    <div
      class="relative flex flex-col w-full h-full text-slate-700 bg-white shadow-md rounded-xl"
    >
      <div class="relative mx-4 mt-4">
        <div class="flex items-center justify-between">
          <div class="flex flex-row gap-7 items-center">
            <div>
              <h3 class="text-lg font-semibold text-slate-800">
                Site maintenance report
              </h3>
              <p class="text-slate-500">Review each log or download the file</p>
            </div>

            <div class="flex flex-row gap-4 items-center">
              <button
                @click="prevPage"
                :disabled="offset === 0"
                class="p-3 m-0 leading-none border border-black rounded-xl transition-colors hover:bg-black hover:text-white disabled:bg-gray-100 disabled:text-black"
              >
                Previous
              </button>
              <button
                @click="nextPage"
                :disabled="offset + limit >= totalLogs"
                class="p-3 m-0 leading-none border border-black rounded-xl transition-colors hover:bg-black hover:text-white disabled:bg-gray-100 disabled:text-black"
              >
                Next
              </button>

              <p class="m-0" v-if="totalLogs">
                Page {{ currentPage }} of {{ Math.ceil(totalLogs / 10) }}
              </p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search logs..."
              class="px-3 !min-h-[36px] py-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            />
            <button
              @click="openstruckLogsModal()"
              class="px-4 py-2 border min-h-[35px] border-green-900 text-green-900 rounded hover:text-green-700 hover:border-green-700 focus:outline-none"
            >
              Export
            </button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="p-0 overflow-scroll">
        <table
          id="export-table"
          class="w-full mt-4 text-left table-auto border-collapse"
          style="table-layout: fixed"
        >
          <thead>
            <tr>
              <th class="p-4 border-y border-slate-200 bg-slate-50 w-1/6">
                Name
              </th>
              <th class="p-4 border-y border-slate-200 bg-slate-50 w-1/6">
                Email
              </th>
              <th class="p-4 border-y border-slate-200 bg-slate-50 w-1/6">
                Role
              </th>
              <th class="p-4 border-y border-slate-200 bg-slate-50 w-1/6">
                Action
              </th>
              <th class="p-4 border-y border-slate-200 bg-slate-50 w-1/6">
                Action Taken
              </th>
              <th class="p-4 border-y border-slate-200 bg-slate-50 w-1/6">
                Action Time
              </th>
            </tr>
          </thead>
          <tbody>
            <!-- Skeleton Rows -->
            <template v-if="isSearching">
              <tr v-for="i in 10" :key="i" class="animate-pulse">
                <td class="p-4 border-b border-slate-200">
                  <div class="h-4 bg-slate-200 rounded"></div>
                </td>
                <td class="p-4 border-b border-slate-200">
                  <div class="h-4 bg-slate-200 rounded"></div>
                </td>
                <td class="p-4 border-b border-slate-200">
                  <div class="h-4 bg-slate-200 rounded"></div>
                </td>
                <td class="p-4 border-b border-slate-200">
                  <div class="h-4 bg-slate-200 rounded"></div>
                </td>
                <td class="p-4 border-b border-slate-200">
                  <div class="h-4 bg-slate-200 rounded"></div>
                </td>
                <td class="p-4 border-b border-slate-200">
                  <div class="h-4 bg-slate-200 rounded"></div>
                </td>
                <td class="p-4 border-b border-slate-200">
                  <div class="h-4 bg-slate-200 rounded"></div>
                </td>
              </tr>
            </template>
            <!-- Data Rows -->
            <template v-else>
              <tr
                v-for="user in filteredLogs"
                :key="user.user_id"
                class="hover:bg-slate-50"
              >
                <td class="p-4 border-b border-slate-200">
                  {{ user.user_name }}
                </td>
                <td class="p-4 border-b border-slate-200">{{ user.email }}</td>
                <td class="p-4 border-b border-slate-200">
                  {{ user.role }}
                </td>
                <td class="p-4 border-b border-slate-200">
                  <div class="w-max">
                    <div
                      :class="renderColor(user.action_type)"
                      class="relative grid items-center px-2 py-1 font-sans text-xs font-bold text-green-900 uppercase rounded-md select-none whitespace-nowrap"
                    >
                      <span>{{ translateAction(user.action_type) }}</span>
                    </div>
                  </div>
                </td>
                <td class="p-4 border-b border-slate-200">
                  {{ user.action_taken }}
                </td>
                <td class="p-4 border-b border-slate-200">
                  {{ user.action_time }}
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="export max-w-[1240px] px-16 py-16"></div>

  <div
    v-if="showstruckLogsModal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
  >
    <div class="bg-white rounded-lg shadow-lg w-1/3 max-w-md p-6">
      <!-- Modal header -->
      <div class="flex items-center justify-between py-4 border-b rounded-t">
        <h3 class="text-lg font-semibold text-gray-900">Configure Export</h3>
        <button
          @click="closestruckLogsModal"
          type="button"
          class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
        >
          <svg
            class="w-3 h-3"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 14 14"
          >
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
            />
          </svg>
          <span class="sr-only">Close modal</span>
        </button>
      </div>

      <!-- Modal body -->
      <div class="mt-4">
        <!-- Select Date Range -->
        <div class="mb-4">
          <label
            for="startDate"
            class="block mb-2 text-sm font-medium text-gray-900"
            >Start Date</label
          >
          <input
            type="date"
            id="startDate"
            v-model="struckLogs.startDate"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
          />
        </div>
        <div class="mb-4">
          <label
            for="endDate"
            class="block mb-2 text-sm font-medium text-gray-900"
            >End Date</label
          >
          <input
            type="date"
            id="endDate"
            v-model="struckLogs.endDate"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
          />
        </div>
        <!-- Select User -->
        <div class="mb-4">
          <label for="user" class="block mb-2 text-sm font-medium text-gray-900"
            >Select User</label
          >
          <select
            id="user"
            v-model="struckLogs.user"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
          >
            <option value="">All Users</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div>

        <div class="mb-4">
          <label
            for="summary"
            class="block mb-2 text-sm font-medium text-gray-900"
            >Summary:</label
          >
          <textarea
            id="summary"
            v-model="struckLogs.summary"
            rows="4"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
          ></textarea>
        </div>

        <div class="mb-4">
          <label
            for="recommendations"
            class="block mb-2 text-sm font-medium text-gray-900"
            >Recommendations:</label
          >
          <textarea
            id="recommendations"
            v-model="struckLogs.recommendations"
            rows="4"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
          ></textarea>
        </div>
      </div>

      <div class="mt-6 flex flex-col gap-6 justify-end">
        <button
          @click="exportToPDF"
          :disabled="loading"
          class="flex w-fit self-end items-center gap-4 text-white bg-blue-700 disabled:bg-gray-400 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5"
        >
          <div v-if="loading" role="status">
            <svg
              aria-hidden="true"
              class="w-6 h-6 text-gray-200 animate-spin fill-gray-600"
              viewBox="0 0 100 101"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                fill="currentColor"
              />
              <path
                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                fill="currentFill"
              />
            </svg>
            <span class="sr-only">Loading...</span>
          </div>
          {{ loading ? "Exporting..." : "Export to PDF" }}
        </button>
        <div
          v-if="loading"
          class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50"
          role="alert"
        >
          <span class="font-medium">Rendering Report...</span> We are fetching
          your Core Web Vitals from Google. This might take a while...
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import useActions from "../../scripts/useActions";
import html2pdf from "html2pdf.js";
import axios from "axios";

const showstruckLogsModal = ref(false);
const searchQuery = ref("");
const isSearching = ref(false);
const loading = ref(false);
const settingsData = window.struckData.plugin_options;
const users = ref([]);
const reportLogs = ref([]);
const logs = ref([]);
const totalLogs = ref(0);
const offset = ref(0);
const limit = ref(10);
const currentPage = ref(1);
const { translateAction, renderColor } = useActions();

const api = axios.create({
  baseURL: `/wp-json/struck/v1/`,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
    "X-WP-Nonce": window.wpApiSettings.nonce,
  },
  timeout: 100000,
});

const getPagespeedData = async () => {
  loading.value = true;
  const response = await api.get("pagespeed");
  window.struckData.page_speed_data = response.data;
};

const fetchUsers = async () => {
  try {
    const response = await api.get("users");
    users.value = response.data;
  } catch (error) {
    console.error("Error Fetching the logs:", error);
  }
};

const fetchLogs = async (
  offset = 0,
  limit = 10,
  id = null,
  startDate = null,
  endDate = null,
  report = false
) => {
  isSearching.value = true;

  try {
    const response = await api.get("logs", {
      params: {
        offset: offset,
        limit: limit,
        user_id: id,
        start_date: startDate,
        end_date: endDate,
      },
    });

    const logsResponse = await api.get("total");

    if (report) {
      reportLogs.value = response.data;
    } else {
      logs.value = response.data;
      totalLogs.value = logsResponse.data;
    }

    setTimeout(() => {
      isSearching.value = false;
    }, 500);
  } catch (error) {
    console.error("Error Fetching the logs:", error);
  }
};

const nextPage = () => {
  if (offset.value + limit.value < totalLogs.value) {
    currentPage.value += 1;
    offset.value += limit.value;
    fetchLogs(offset.value, limit.value);
  }
};

const prevPage = () => {
  if (offset.value > 0) {
    currentPage.value -= 1;
    offset.value -= limit.value;
    fetchLogs(offset.value, limit.value);
  }
};

const struckLogs = ref({
  startDate: "",
  endDate: "",
  reportType: "",
  activity: "",
  user: "",
  summary: "",
  recommendations: "",
});

const filteredLogs = computed(() => {
  return logs.value.filter((user) => {
    const search = searchQuery.value.toLowerCase();
    return (
      user.user_name.toLowerCase().includes(search) ||
      user.email.toLowerCase().includes(search) ||
      user.role.toLowerCase().includes(search) ||
      user.action_type.toLowerCase().includes(search) ||
      user.action_taken.toLowerCase().includes(search) ||
      user.action_time.toLowerCase().includes(search)
    );
  });
});

watch(searchQuery, (newValue) => {
  if (newValue) {
    isSearching.value = true;
    setTimeout(() => {
      isSearching.value = false;
    }, 500);
  } else {
    isSearching.value = false;
  }
});

function openstruckLogsModal() {
  showstruckLogsModal.value = true;
}

function closestruckLogsModal() {
  showstruckLogsModal.value = false;
}

function wc_hex_is_light(color) {
  const hex = color.replace("#", "");
  const c_r = parseInt(hex.substring(0, 2), 16);
  const c_g = parseInt(hex.substring(2, 4), 16);
  const c_b = parseInt(hex.substring(4, 6), 16);
  const brightness = (c_r * 299 + c_g * 587 + c_b * 114) / 1000;
  return brightness > 155;
}

const backgroundColor = settingsData.color_skin;
const textColor = wc_hex_is_light(backgroundColor) ? "#000" : "#fff";

function scoreColor(score) {
  if (score >= 85 && score <= 100) {
    return "good";
  } else if (score >= 65 && score < 85) {
    return "medium";
  } else if (score >= 0 && score < 65) {
    return "bad";
  } else {
    return "";
  }
}
async function exportToPDF() {
  await getPagespeedData();
  await fetchLogs(
    0,
    10,
    struckLogs.value.user ? struckLogs.value.user : null,
    struckLogs.value.startDate,
    struckLogs.value.endDate,
    true
  );

  const pluginsData = window.struckData.installed_plugins || [];

  const wrapperElement = document.createElement("div");
  wrapperElement.style.fontFamily = "Arial, sans-serif";
  wrapperElement.style.fontSize = "12px";
  wrapperElement.style.lineHeight = "1.5";
  wrapperElement.classList.add = "main-container";

  // Header
  const header = document.createElement("div");
  header.style.alignItems = "center";
  header.style.marginBottom = "20px";
  header.style.padding = "10px 0px";

  const companyInfo = document.createElement("div");
  companyInfo.innerHTML = `
     <div style="
      position: relative;
      bottom: 0;
      left: 0;
      right: 0;
      font-size: 10px;
      background-color: ${settingsData.color_skin};
      text-align: center;
      padding: 0px;
    ">
        <table style="width: 100%; border-collapse: collapse;" cellpadding="20">
            <tr>
                <td>
                    <div style="color: ${textColor} !important; z-index: 1; text-align: left">
                        <div>${settingsData.address_information}</div>
                    </div>
                </td>
                <td style:"text-align: right">
                    <div style="z-index: 1; text-align: right">
                        <img style="width: 180px; height: auto"  src="${settingsData.company_logo.url}" />
                    </div>
                </td>
            </tr>
        </table>
    </div>
`;

  header.appendChild(companyInfo);

  // Title
  const title = document.createElement("h1");
  title.innerText = "Website Maintenance and Core Vitals Report";
  title.style.textAlign = "left";
  title.style.fontSize = "18px";
  title.style.marginBottom = "20px";
  title.style.borderBottom = "5px solid ${settingsData.color_skin}";

  // Information
  const projectSummary = document.createElement("div");
  projectSummary.style.marginBottom = "20px";
  projectSummary.style.paddingTop = "20px";
  projectSummary.innerHTML = `
    <h3 style="border-bottom: 1px solid ${
      settingsData.color_skin
    }; margin-bottom: 10px; padding-bottom: 3px">Project Information</h3>
    <table style="width: 100%; border-collapse: collapse;">
      <tr>
        <td><strong>Report Date:</strong> ${new Date().toLocaleDateString()}</td>
        <td><strong>Developer Assigned:</strong> ${
          settingsData.assigned_developer
        }</td>
        <td>
            <strong>Development Director:</strong> ${
              settingsData.development_director
            }
        </td>
        <td>
            <strong>Contact Email:</strong> <a href="mailto:${
              settingsData.contact_email
            }">${settingsData.contact_email}</a>
        </td>
      </tr>
    </table>
  `;

  // Summary
  const statusSummary = document.createElement("div");
  statusSummary.style.marginBottom = "20px";
  statusSummary.style.paddingTop = "20px";
  statusSummary.innerHTML = `
    <h3 style="border-bottom: 1px solid ${settingsData.color_skin}; margin-bottom: 10px; padding-bottom: 5px">Summary</h3>
    <p>${struckLogs.value.summary}</p>
  `;

  // Table start
  const projectOverview = document.createElement("div");
  projectOverview.style.marginBottom = "20px";
  projectOverview.style.paddingTop = "20px";
  projectOverview.innerHTML = `
    <h3 style="border-bottom: 1px solid ${settingsData.color_skin}; margin-bottom: 10px; padding-bottom: 5px">Logs</h3>
  `;

  // const tableElement = document.getElementById("export-table");
  const logsTableContainer = document.createElement("div");
  if (!filteredLogs.value.length) {
    alert("No data available to export.");
    return;
  }

  logsTableContainer.innerHTML = `
  <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; text-align: left">
    <thead style="background-color: ${backgroundColor}; color: ${textColor}">
      <tr>
        <th style="border: 1px solid #ccc; padding: 8px;">Name</th>
        <th style="border: 1px solid #ccc; padding: 8px;">Email</th>
        <th style="border: 1px solid #ccc; padding: 8px;">Role</th>
        <th style="border: 1px solid #ccc; padding: 8px;">Action</th>
        <th style="border: 1px solid #ccc; padding: 8px;">Action Taken</th>
        <th style="border: 1px solid #ccc; padding: 8px;">Action Time</th>
      </tr>
    </thead>
    <tbody>
      ${reportLogs.value
        .map(
          (log) => `
          <tr>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px;">${
              log?.user_name || "-"
            }</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px;">${
              log?.email || "-"
            }</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px;">${
              log?.role || "-"
            }</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px;">
              <div class="w-max">
                <div class="relative grid items-center px-2 py-1 font-sans text-xs font-bold uppercase rounded-md select-none whitespace-nowrap
                  ${renderColor(log?.action_type)}">
                  <span>${translateAction(log?.action_type)}</span>
                </div>
              </div>
            </td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px;">${
              log?.action_taken || "-"
            }</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px;">${
              log?.action_time || "-"
            }</td>
          </tr>
        `
        )
        .join("")}
    </tbody>
  </table>
`;

  // projectOverview.appendChild(clonedTable);
  // Table ends

  // Plugins Table
  const installedPluginsHeader = document.createElement("div");
  installedPluginsHeader.style.marginBottom = "20px";
  installedPluginsHeader.style.paddingTop = "20px";
  installedPluginsHeader.innerHTML = `
    <h3 style="border-bottom: 1px solid ${settingsData.color_skin}; margin-bottom: 10px; padding-bottom: 5px">WordPress repository installed plugins</h3>
  `;

  const pluginsTableContainer = document.createElement("div");
  pluginsTableContainer.innerHTML = `
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; text-align: left">
        <thead style="background-color: ${backgroundColor}; color: ${textColor}">
            <tr>
                <th style="border: 1px solid #ccc; padding: 8px;">Plugin Name</th>
                <th style="border: 1px solid #ccc; padding: 8px;">WP Required Version</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Tested Up To</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Requires PHP</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Rating</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Last Repo Update</th>
            </tr>
        </thead>
    <tbody>
        ${pluginsData
          .map(
            (plugin) => `
          <tr>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${plugin.plugin_name}</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${plugin.WP_required_version}</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${plugin.tested_up_to_WP_version}</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${plugin.requires_php}</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${plugin.rating}%</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${plugin.last_repo_update}</td>
          </tr>
        `
          )
          .join("")}
      </tbody>
    </table>
  `;

  // Wordfence
  const wordfenceData = window.struckData.wordfence_logs;

  // if (wordfenceData) {
  const wordfence = document.createElement("div");
  wordfence.style.marginTop = "20px";
  wordfence.style.paddingTop = "20px";
  wordfence.innerHTML = `
    <h3 style="border-bottom: 1px solid ${backgroundColor}; margin-bottom: 10px; padding-bottom: 5px">Virus Scan (Powered by Wordfence™)</h3>
    <p><b>*${
      wordfenceData?.lastMessage
        ? wordfenceData?.lastMessage
        : "No scan has been run lately"
    }</b></p>
  `;

  const wordfenceTableContainer = document.createElement("div");
  wordfenceTableContainer.innerHTML = `
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; text-align: left">
        <thead style="background-color: ${backgroundColor}; color: ${textColor}">
            <tr>
                <th style="border: 1px solid #ccc; padding: 8px;">Plugin Name</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Version</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Available Update</th>
                <th style="border: 1px solid #ccc; padding: 8px;">New Version</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Date</th>
                <th style="border: 1px solid #ccc; padding: 8px;">Message</th>
            </tr>
        </thead>
    <tbody>
        ${wordfenceData?.issues?.new
          .map(
            (plugin) => `
          <tr>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${
              plugin?.data?.Title
            }</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${
              plugin?.data?.Version
            }</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${
              plugin?.data?.updateAvailable ? "Yes" : "No"
            }</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${
              plugin?.data?.newVersion ? plugin?.data?.newVersion : "-"
            }</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${
              plugin?.displayTime ? plugin?.displayTime : "-"
            }</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${
              plugin?.shortMsg ? plugin?.shortMsg : "-"
            }</td>
          </tr>
        `
          )
          .join("")}
      </tbody>
    </table>
  `;
  // }

  //   Page Speed Heading
  const pageSpeedDataHeading = document.createElement("div");
  pageSpeedDataHeading.style.marginTop = "20px";
  pageSpeedDataHeading.style.paddingTop = "20px";
  pageSpeedDataHeading.innerHTML = `
    <h3 style="border-bottom: 1px solid ${settingsData.color_skin}; margin-bottom: 10px; padding-bottom: 5px">Site Page Speed and metrics (Powered by PageSpeed Insights™)</h3>
  `;

  // Page Speed Data
  const pageSpeedData = document.createElement("div");
  pageSpeedData.style.marginBottom = "20px";
  pageSpeedData.style.paddingTop = "20px";
  pageSpeedData.innerHTML = `
        <style>
            @property --percentage {
            syntax: "<integer>";
            initial-value: 0;
            inherits: false;
            }

            .counter {
            animation: counter 0s ease-out;
            animation-fill-mode: forwards;
            counter-reset: num var(--percentage);
            }
            .counter::after {
            content: counter(num) "%";
            }

            @keyframes counter {
            from {
                --percentage: 0;
            }
            to {
                --percentage: var(--counter-end);
            }
            }

            .percentage-chart {
            width: 130px;
            height: auto;
            position: relative;
            max-width: 100px;
            }
            .percentage-chart-bg {
            fill: none;
            stroke: #C9C9CC; /* Circle stroke color unfilled */
            stroke-width: 3;
            }

            .good .counter, .good .percentage-chart-stroke {
            stroke: #49BF78;
            color: #49BF78;
            }
            .medium .counter, .medium .percentage-chart-stroke {
            stroke: #FF864B;
            color: #FF864B;
            }
            .bad .counter, .bad .percentage-chart-stroke {
            stroke: #FF4B4B;
            color: #FF4B4B;
            }

            .percentage-chart-stroke {
            fill: none;
            stroke-width: 3;
            stroke: #49BF78; /* Circle stroke color fill */
            stroke-linecap: round;
            animation: progress 0s ease-out forwards;
            }

            .counter {
            position: absolute;
            left: 50%;
            top: 50%;
            font-size: 24px;
            color: var(--fig-color); /* Internal number color */
            transform: translate3d(-50%, -50%, 0);
            }

            @keyframes progress {
            0% {
                stroke-dasharray: 0 100;
            }
            }

            /** Optional styling **/
            .time-breakdown {
            display: flex;
            gap: 35px;
            justify-content: center;
            }
            .time-breakdown-chart {
            display: flex;
            font-family: sans-serif;
            flex: 1;
            }
            .time-breakdown-chart p {
            margin: 6px 0 0 0;
            color: #909090;
            font-size: 14px;
            }
            .time-breakdown-chart h4 {
            margin: 0;
            color: #1B1C20;
            font-size: 16px;
            font-weight: normal;
            }

            .percentage-chart-meeting .percentage-chart-stroke {
            stroke: #ff9e00;
            }
            .percentage-chart-distraction .percentage-chart-stroke {
            stroke: #f74141;
            }

            .percentage-chart-meeting .counter {
                color: #ff9e00;
            }

            .percentage-chart-distraction .counter {
                color: #f74141;
            }

            .chart-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin-left: 16px;
            }

        </style>
        <div style="display: flex; flex-direction: row; column-gap: 40px">
            <div>
                <img src="${
                  window.struckData.page_speed_data.lighthouseResult.audits[
                    `final-screenshot`
                  ].details.data
                }"/>
            </div>
            <div style="display: flex; flex-direction: row; flex: 1; flex-wrap: wrap; align-items: center">
                <div class="time-breakdown-chart ${scoreColor(
                  window.struckData.page_speed_data.lighthouseResult.categories
                    .performance.score * 100
                )}">
                    <!-- Focus chart -->
                    <div class="percentage-chart percentage-chart-focus">
                    <svg viewBox="0 0 36 36">
                        <path class="percentage-chart-bg" d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="percentage-chart-stroke" stroke-dasharray="${Math.round(
                          window.struckData.page_speed_data.lighthouseResult
                            .categories.performance.score * 100
                        )}, 100" d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="counter" style="--counter-end:${Math.round(
                      window.struckData.page_speed_data.lighthouseResult
                        .categories.performance.score * 100
                    )};"></div>
                    </div>
                    <div class="chart-info">
                    <h4>Performance</h4>
                    <p>Overall Score</p>
                    </div>
                </div>
                <div class="time-breakdown-chart ${scoreColor(
                  window.struckData.page_speed_data?.lighthouseResult.audits[
                    "cumulative-layout-shift"
                  ].score * 100
                )}">
                    <!-- Focus chart -->
                    <div class="percentage-chart percentage-chart-focus">
                    <svg viewBox="0 0 36 36">
                        <path class="percentage-chart-bg" d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="percentage-chart-stroke" stroke-dasharray="${Math.round(
                          window.struckData?.page_speed_data.lighthouseResult
                            .audits["cumulative-layout-shift"].score * 100
                        )}, 100" d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="counter" style="--counter-end:${Math.round(
                      window.struckData?.page_speed_data.lighthouseResult
                        .audits["cumulative-layout-shift"].score * 100
                    )};"></div>
                    </div>
                    <div class="chart-info">
                    <h4>CLS</h4>
                    <p>Cumulative Layout Shift</p>
                    </div>
                </div>
                <div class="time-breakdown-chart ${scoreColor(
                  window.struckData?.page_speed_data.lighthouseResult.audits[
                    "first-contentful-paint"
                  ].score * 100
                )}">
                    <!-- Focus chart -->
                    <div class="percentage-chart percentage-chart-focus">
                    <svg viewBox="0 0 36 36">
                        <path class="percentage-chart-bg" d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="percentage-chart-stroke" stroke-dasharray="${Math.round(
                          window.struckData?.page_speed_data.lighthouseResult
                            .audits["first-contentful-paint"].score * 100
                        )}, 100" d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="counter" style="--counter-end:${Math.round(
                      window.struckData?.page_speed_data.lighthouseResult
                        .audits["first-contentful-paint"].score * 100
                    )};"></div>
                    </div>
                    <div class="chart-info">
                    <h4>FCP</h4>
                    <p>First Contentful Paint</p>
                    </div>
                </div>
                <div class="time-breakdown-chart ${scoreColor(
                  window.struckData?.page_speed_data.lighthouseResult.audits[
                    "first-contentful-paint"
                  ].score * 100
                )}">
                    <!-- Focus chart -->
                    <div class="percentage-chart percentage-chart-focus">
                    <svg viewBox="0 0 36 36">
                        <path class="percentage-chart-bg" d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="percentage-chart-stroke" stroke-dasharray="${Math.round(
                          window.struckData?.page_speed_data.lighthouseResult
                            .audits["largest-contentful-paint"].score * 100
                        )}, 100" d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="counter" style="--counter-end:${Math.round(
                      window.struckData?.page_speed_data.lighthouseResult
                        .audits["largest-contentful-paint"].score * 100
                    )};"></div>
                    </div>
                    <div class="chart-info">
                    <h4>LCP</h4>
                    <p>Largest Contentful Paint</p>
                    </div>
                </div>
            </div>
        </div>
    `;

  // Recommendations
  const recommendations = document.createElement("div");
  recommendations.style.marginTop = "20px";
  recommendations.style.paddingTop = "20px";
  recommendations.innerHTML = `
    <h3 style="border-bottom: 1px solid ${settingsData.color_skin}; margin-bottom: 10px; padding-bottom: 5px">Recommendations</h3>
    <p>${struckLogs.value.recommendations}</p>
  `;

  // Footer
  const footer = document.createElement("div");
  footer.innerHTML = `
    <div
    style="
      position: relative;
      bottom: 0;
      left: 0;
      right: 0;
      font-size: 10px;
      color: #666666;
      text-align: center;
      padding: 10px 0;
      border-top: 1px solid #cccccc;
      margin-top: 100px;
    ">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td>
                    <div style="color: #666666; z-index: 1; text-align: left">
                        Developed by Struck
                    </div>
                </td>
                <td style:"text-align: right">
                    <div style="z-index: 1; text-align: right">
                        <img style="width: 50px; height: auto"  src="data:image/png;base64,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" />
                    </div>
                </td>
            </tr>
        </table>
    </div>
  `;
  footer.style.position = "relative";
  footer.style.marginTop = "30px";

  // Appends
  wrapperElement.appendChild(header);
  wrapperElement.appendChild(title);
  wrapperElement.appendChild(projectSummary);
  if (struckLogs.summary) {
    wrapperElement.appendChild(statusSummary);
  }
  wrapperElement.appendChild(projectOverview);
  wrapperElement.appendChild(logsTableContainer);
  wrapperElement.appendChild(installedPluginsHeader);
  wrapperElement.appendChild(pluginsTableContainer);
  wrapperElement.appendChild(wordfence);
  wrapperElement.appendChild(wordfenceTableContainer);
  wrapperElement.appendChild(pageSpeedDataHeading);
  wrapperElement.appendChild(pageSpeedData);
  if (struckLogs.recommendations) {
    wrapperElement.appendChild(recommendations);
  }
  wrapperElement.appendChild(footer);

  const exportedDocument = document.querySelector(".export");

  exportedDocument.appendChild(wrapperElement);

  const { width, height } = exportedDocument.getBoundingClientRect();

  // Save
  const options = {
    margin: [10, 10],
    filename: `Website Status Report ${new Date().toISOString()}.pdf`,
    html2canvas: { scale: 2 },
    pageBreak: {
      mode: ["avoid-all"],
      autoPaging: false,
    },
    jsPDF: {
      unit: "px",
      format: [width, height + 500],
      orientation: "portrait",
    },
  };

  await html2pdf().set(options).from(exportedDocument).save();
  exportedDocument.innerHTML = "";
  loading.value = false;
  closestruckLogsModal();
}

onMounted(() => {
  fetchUsers();
  fetchLogs(offset.value, limit.value);
});
</script>

<style scoped>
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 1.5s infinite;
}
</style>
