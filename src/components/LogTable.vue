<template>
  <div class="max-w-full mx-auto mt-[10px] mr-[20px] mb-0 ml-[2px]">
    <div
      class="relative flex flex-col w-full h-full text-slate-700 bg-white shadow-md rounded-xl"
    >
      <template v-if="hasRequiredSettings(settingsData)">
        <div class="relative mx-4 mt-4">
          <div class="flex items-center justify-between">
            <div class="flex flex-row gap-7 items-center">
              <div>
                <h3 class="text-lg font-semibold text-slate-800">
                  Site maintenance report
                </h3>
                <p class="text-slate-500">Review each log or download a copy</p>
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
            <div class="flex flex-col items-center justify-end gap-2">
              <div class="flex items-center gap-2 self-end">
                <button
                  @click="showModal = true"
                  class="px-4 py-2 border min-h-[35px] border-blue-900 text-blue-900 rounded hover:text-blue-700 hover:border-blue-700 focus:outline-none"
                >
                  Add Manual Entry
                </button>

                <!-- Modal -->
                <div
                  v-if="showModal"
                  class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                >
                  <div class="bg-white p-6 rounded-lg w-full max-w-md">
                    <h2 class="text-lg font-semibold mb-4">Manual Entry</h2>

                    <!-- Dropdown -->
                    <label class="block mb-2 font-medium">Select Option</label>
                    <select
                      v-model="selectedOption"
                      class="w-full border rounded px-2 py-1 mb-4"
                    >
                      <option disabled value="">Please select an action</option>
                      <option value="SITE_REVIEW">Site Review</option>
                      <option value="UPDATED_CONFIG">
                        Configuration Updated
                      </option>
                    </select>

                    <!-- Textbox -->
                    <label class="block mb-2 font-medium">Comment</label>
                    <textarea
                      v-model="comment"
                      rows="3"
                      class="w-full border rounded px-2 py-1 mb-4"
                      placeholder="Enter your comment..."
                    ></textarea>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-2">
                      <button
                        @click="submitEntry"
                        class="bg-blue-500 border-none font-bold text-white px-4 py-2 rounded flex flex-row gap-2"
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
                        {{ loading ? "Adding Manual Entry" : "Submit" }}
                      </button>
                      <button
                        @click="showModal = false"
                        class="bg-red-400 border border-none font-bold text-white px-4 py-2 rounded flex flex-row gap-2"
                      >
                        Cancel
                      </button>
                    </div>
                  </div>
                </div>
                <!-- End Modal -->

                <input
                  type="text"
                  v-model="searchQuery"
                  placeholder="Search logs..."
                  class="px-3 !min-h-[36px] py-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                />
                <button
                  @click="openstruckLogsModal()"
                  class="bg-green-500/20 border border-green-900 font-bold text-green-900 px-4 py-2 rounded"
                >
                  Export
                </button>
              </div>
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
                  <td class="p-4 border-b border-slate-200">
                    {{ user.email.includes("no@email.com") ? "-" : user.email }}
                  </td>
                  <td class="p-4 border-b border-slate-200">
                    {{ user.role }}
                  </td>
                  <td class="p-4 border-b border-slate-200">
                    <div class="w-max">
                      <div
                        :class="
                          renderColor(user.action_type) === 'green'
                            ? 'bg-green-500/20 border border-green-900 text-green-900'
                            : renderColor(user.action_type) === 'red'
                            ? 'bg-red-50 border border-red-900 text-red-900'
                            : 'bg-yellow-100 border border-yellow-900 text-yellow-900'
                        "
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
      </template>
      <template v-else>
        <section class="bg-white">
          <div
            class="gap-8 items-center py-8 px-4 mx-auto max-w-screen-xl xl:gap-16 md:grid md:grid-cols-2 sm:py-16 lg:px-6"
          >
            <svg
              class="w-full"
              xmlns="http://www.w3.org/2000/svg"
              width="992"
              height="715"
              viewBox="0 0 992 715"
              fill="none"
            >
              <g clip-path="url(#clip0_2881_4057)">
                <rect width="992" height="715" fill="white" />
                <g opacity="0.8" filter="url(#filter0_dd_2881_4057)">
                  <rect width="992" height="36" fill="white" />
                  <path
                    d="M15.5 22.1667V13.8334M15.5 22.1667C15.5 22.6088 15.3244 23.0327 15.0118 23.3453C14.6993 23.6578 14.2754 23.8334 13.8333 23.8334H12.1667C11.7246 23.8334 11.3007 23.6578 10.9882 23.3453C10.6756 23.0327 10.5 22.6088 10.5 22.1667V13.8334C10.5 13.3914 10.6756 12.9675 10.9882 12.6549C11.3007 12.3423 11.7246 12.1667 12.1667 12.1667H13.8333C14.2754 12.1667 14.6993 12.3423 15.0118 12.6549C15.3244 12.9675 15.5 13.3914 15.5 13.8334M15.5 22.1667C15.5 22.6088 15.6756 23.0327 15.9882 23.3453C16.3007 23.6578 16.7246 23.8334 17.1667 23.8334H18.8333C19.2754 23.8334 19.6993 23.6578 20.0118 23.3453C20.3244 23.0327 20.5 22.6088 20.5 22.1667M15.5 13.8334C15.5 13.3914 15.6756 12.9675 15.9882 12.6549C16.3007 12.3423 16.7246 12.1667 17.1667 12.1667H18.8333C19.2754 12.1667 19.6993 12.3423 20.0118 12.6549C20.3244 12.9675 20.5 13.3914 20.5 13.8334M20.5 22.1667V13.8334M20.5 22.1667C20.5 22.6088 20.6756 23.0327 20.9882 23.3453C21.3007 23.6578 21.7246 23.8334 22.1667 23.8334H23.8333C24.2754 23.8334 24.6993 23.6578 25.0118 23.3453C25.3244 23.0327 25.5 22.6088 25.5 22.1667V13.8334C25.5 13.3914 25.3244 12.9675 25.0118 12.6549C24.6993 12.3423 24.2754 12.1667 23.8333 12.1667H22.1667C21.7246 12.1667 21.3007 12.3423 20.9882 12.6549C20.6756 12.9675 20.5 13.3914 20.5 13.8334"
                    stroke="#9CA3AF"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M235.398 9.7644H37.7452C36.2291 9.7644 35 10.9935 35 12.5096V23.4903C35 25.0064 36.2291 26.2355 37.7452 26.2355H235.398C236.914 26.2355 238.143 25.0064 238.143 23.4903V12.5096C238.143 10.9935 236.914 9.7644 235.398 9.7644Z"
                    fill="#E4E4E7"
                  />
                  <path
                    d="M934.9 19.1587L934.753 19.3051L934.329 19.7293C934.329 19.7293 934.329 19.7293 934.329 19.7293C934.315 19.7433 934.306 19.7611 934.302 19.7804C934.298 19.7998 934.3 19.8199 934.308 19.8382C934.315 19.8565 934.328 19.8721 934.344 19.8831C934.361 19.8941 934.38 19.8999 934.4 19.9L934.9 19.1587ZM934.9 19.1587V18.9516M934.9 19.1587V18.9516M934.9 18.9516V16.8C934.9 15.9778 935.227 15.1893 935.808 14.6079C936.389 14.0266 937.178 13.7 938 13.7C938.822 13.7 939.611 14.0266 940.192 14.6079C940.773 15.1893 941.1 15.9778 941.1 16.8V18.9516V19.1587M934.9 18.9516L941.1 19.1587M941.1 19.1587L941.246 19.3051M941.1 19.1587L941.246 19.3051M941.246 19.3051L941.671 19.7293C941.671 19.7293 941.671 19.7293 941.671 19.7293C941.685 19.7433 941.694 19.7611 941.698 19.7804C941.702 19.7998 941.7 19.8199 941.692 19.8382C941.685 19.8565 941.672 19.8721 941.655 19.8831C941.639 19.8941 941.62 19.8999 941.6 19.9L941.246 19.3051ZM941.6 19.9H934.4H941.6ZM938 22.3C937.655 22.3 937.324 22.163 937.081 21.9192C936.959 21.7979 936.865 21.6552 936.8 21.5H939.2C939.135 21.6552 939.04 21.7979 938.919 21.9192C938.675 22.163 938.345 22.3 938 22.3Z"
                    fill="#6B7280"
                    stroke="#6B7280"
                  />
                  <path
                    d="M955 13.8C954.682 13.8 954.377 13.9265 954.152 14.1515C953.926 14.3766 953.8 14.6818 953.8 15V16.2C953.8 16.5183 953.926 16.8235 954.152 17.0486C954.377 17.2736 954.682 17.4 955 17.4H956.2C956.518 17.4 956.824 17.2736 957.049 17.0486C957.274 16.8235 957.4 16.5183 957.4 16.2V15C957.4 14.6818 957.274 14.3766 957.049 14.1515C956.824 13.9265 956.518 13.8 956.2 13.8H955ZM955 18.6C954.682 18.6 954.377 18.7265 954.152 18.9515C953.926 19.1766 953.8 19.4818 953.8 19.8V21C953.8 21.3183 953.926 21.6235 954.152 21.8486C954.377 22.0736 954.682 22.2 955 22.2H956.2C956.518 22.2 956.824 22.0736 957.049 21.8486C957.274 21.6235 957.4 21.3183 957.4 21V19.8C957.4 19.4818 957.274 19.1766 957.049 18.9515C956.824 18.7265 956.518 18.6 956.2 18.6H955ZM958.6 15C958.6 14.6818 958.726 14.3766 958.952 14.1515C959.177 13.9265 959.482 13.8 959.8 13.8H961C961.318 13.8 961.624 13.9265 961.849 14.1515C962.074 14.3766 962.2 14.6818 962.2 15V16.2C962.2 16.5183 962.074 16.8235 961.849 17.0486C961.624 17.2736 961.318 17.4 961 17.4H959.8C959.482 17.4 959.177 17.2736 958.952 17.0486C958.726 16.8235 958.6 16.5183 958.6 16.2V15ZM958.6 19.8C958.6 19.4818 958.726 19.1766 958.952 18.9515C959.177 18.7265 959.482 18.6 959.8 18.6H961C961.318 18.6 961.624 18.7265 961.849 18.9515C962.074 19.1766 962.2 19.4818 962.2 19.8V21C962.2 21.3183 962.074 21.6235 961.849 21.8486C961.624 22.0736 961.318 22.2 961 22.2H959.8C959.482 22.2 959.177 22.0736 958.952 21.8486C958.726 21.6235 958.6 21.3183 958.6 21V19.8Z"
                    fill="#6B7280"
                  />
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M982.8 18C982.8 19.273 982.294 20.4939 981.394 21.3941C980.494 22.2942 979.273 22.8 978 22.8C976.727 22.8 975.506 22.2942 974.606 21.3941C973.706 20.4939 973.2 19.273 973.2 18C973.2 16.7269 973.706 15.506 974.606 14.6058C975.506 13.7057 976.727 13.2 978 13.2C979.273 13.2 980.494 13.7057 981.394 14.6058C982.294 15.506 982.8 16.7269 982.8 18V18ZM979.2 16.2C979.2 16.5182 979.074 16.8234 978.848 17.0485C978.623 17.2735 978.318 17.4 978 17.4C977.682 17.4 977.376 17.2735 977.151 17.0485C976.926 16.8234 976.8 16.5182 976.8 16.2C976.8 15.8817 976.926 15.5765 977.151 15.3514C977.376 15.1264 977.682 15 978 15C978.318 15 978.623 15.1264 978.848 15.3514C979.074 15.5765 979.2 15.8817 979.2 16.2V16.2ZM978 18.6C977.426 18.5998 976.863 18.7646 976.38 19.0748C975.896 19.3849 975.512 19.8274 975.272 20.3496C975.61 20.7423 976.029 21.0574 976.499 21.2732C976.97 21.4891 977.482 21.6005 978 21.6C978.518 21.6005 979.03 21.4891 979.501 21.2732C979.971 21.0574 980.39 20.7423 980.728 20.3496C980.488 19.8274 980.104 19.3849 979.62 19.0748C979.137 18.7646 978.574 18.5998 978 18.6V18.6Z"
                    fill="#6B7280"
                  />
                </g>
                <rect
                  width="992"
                  height="678"
                  transform="translate(0 37)"
                  fill="#F4F4F5"
                />
                <rect
                  width="194"
                  height="678"
                  transform="translate(0 37)"
                  fill="white"
                />
                <rect
                  x="20"
                  y="57"
                  width="8"
                  height="8"
                  rx="4"
                  fill="#D1D5DB"
                />
                <rect
                  x="36"
                  y="57"
                  width="88"
                  height="8"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="20"
                  y="85"
                  width="8"
                  height="8"
                  rx="4"
                  fill="#D1D5DB"
                />
                <rect
                  x="36"
                  y="85"
                  width="132"
                  height="8"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="20"
                  y="113"
                  width="8"
                  height="8"
                  rx="4"
                  fill="#D1D5DB"
                />
                <rect
                  x="36"
                  y="113"
                  width="129"
                  height="8"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="20"
                  y="141"
                  width="8"
                  height="8"
                  rx="4"
                  fill="#D1D5DB"
                />
                <rect
                  x="36"
                  y="141"
                  width="103"
                  height="8"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="20"
                  y="169"
                  width="8"
                  height="8"
                  rx="4"
                  fill="#D1D5DB"
                />
                <rect
                  x="36"
                  y="169"
                  width="130"
                  height="8"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="20"
                  y="197"
                  width="8"
                  height="8"
                  rx="4"
                  fill="#D1D5DB"
                />
                <rect
                  x="36"
                  y="197"
                  width="92"
                  height="8"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="20"
                  y="225"
                  width="8"
                  height="8"
                  rx="4"
                  fill="#D1D5DB"
                />
                <rect
                  x="36"
                  y="225"
                  width="136"
                  height="8"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="77.5"
                  y="694"
                  width="5"
                  height="5"
                  rx="2.5"
                  fill="#D1D5DB"
                />
                <rect
                  x="94.5"
                  y="694"
                  width="5"
                  height="5"
                  rx="2.5"
                  fill="#D1D5DB"
                />
                <rect
                  x="111.5"
                  y="694"
                  width="5"
                  height="5"
                  rx="2.5"
                  fill="#D1D5DB"
                />
                <rect
                  x="214"
                  y="57"
                  width="758"
                  height="243.528"
                  rx="4"
                  fill="white"
                />
                <rect
                  x="230"
                  y="73"
                  width="73"
                  height="7"
                  rx="2"
                  fill="#9CA3AF"
                />
                <rect
                  x="230"
                  y="84"
                  width="39"
                  height="5"
                  rx="2"
                  fill="#D1D5DB"
                />
                <path
                  d="M230.988 191.074C272.735 185.798 269.952 217.138 324.92 221.711C379.887 226.283 395.194 194.277 420.243 185.132C445.291 175.987 456.424 134.316 498.171 137.13C539.918 139.944 560.792 168.137 595.581 171.654C630.371 175.171 637.329 198.497 670.031 215.731C702.733 232.966 732.652 235.076 773.703 231.559C814.755 228.041 825.888 192.481 845.37 191.074C864.852 189.667 882.246 206.625 898.945 210.494C915.644 214.363 920.515 231.559 956 231.559"
                  stroke="#D1D5DB"
                  stroke-width="3"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <path
                  d="M230 240.994C261.932 238.105 288.191 220.486 326.292 217.524C346.599 215.946 362.868 219.018 381.019 223.792C399.17 228.565 395.617 230.626 436.439 234.306C470.126 237.343 478.938 204.601 511.948 205.859C544.959 207.117 560.226 167.751 595.078 171.037C629.93 174.324 636.893 165.827 669.654 181.929C702.415 198.031 732.682 160.499 773.807 157.213C814.932 153.927 825.643 188.805 845.16 187.49C864.677 186.176 882.25 211.029 898.978 214.644C915.707 218.258 920.451 205.127 956 205.127"
                  stroke="#9CA3AF"
                  stroke-width="3"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <rect
                  x="510"
                  y="274.028"
                  width="5"
                  height="5"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="519"
                  y="274.028"
                  width="22"
                  height="5"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="567"
                  y="274.028"
                  width="5"
                  height="5"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="576"
                  y="274.028"
                  width="39"
                  height="5"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="641"
                  y="274.028"
                  width="5"
                  height="5"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="650"
                  y="274.028"
                  width="26"
                  height="5"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="214"
                  y="320.528"
                  width="239.333"
                  height="93"
                  rx="4"
                  fill="white"
                />
                <rect
                  x="224"
                  y="330.528"
                  width="40"
                  height="4"
                  rx="1"
                  fill="#9CA3AF"
                />
                <rect
                  x="224"
                  y="351.528"
                  width="152"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="224"
                  y="360.528"
                  width="81"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="224"
                  y="381.528"
                  width="43"
                  height="14"
                  rx="2"
                  fill="#1A56DB"
                />
                <rect
                  x="231"
                  y="387.028"
                  width="29"
                  height="3"
                  rx="1"
                  fill="white"
                />
                <rect
                  x="274"
                  y="381.528"
                  width="43"
                  height="14"
                  rx="2"
                  fill="#E5E7EB"
                />
                <rect
                  x="281"
                  y="387.028"
                  width="29"
                  height="3"
                  rx="1"
                  fill="white"
                />
                <rect
                  x="473.333"
                  y="320.528"
                  width="239.333"
                  height="93"
                  rx="4"
                  fill="white"
                />
                <rect
                  x="483.333"
                  y="330.528"
                  width="40"
                  height="4"
                  rx="2"
                  fill="#9CA3AF"
                />
                <rect
                  x="633.333"
                  y="331.528"
                  width="14"
                  height="2"
                  rx="1"
                  fill="#D1D5DB"
                />
                <rect
                  x="483.333"
                  y="351.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="527.833"
                  y="351.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="572.333"
                  y="351.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="616.833"
                  y="351.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="483.333"
                  y="363.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#D1D5DB"
                />
                <rect
                  x="527.833"
                  y="363.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="572.333"
                  y="363.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="616.833"
                  y="363.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="483.333"
                  y="375.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#D1D5DB"
                />
                <rect
                  x="527.833"
                  y="375.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="572.333"
                  y="375.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="616.833"
                  y="375.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="483.333"
                  y="387.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#D1D5DB"
                />
                <rect
                  x="527.833"
                  y="387.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="572.333"
                  y="387.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="616.833"
                  y="387.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="483.333"
                  y="399.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#D1D5DB"
                />
                <rect
                  x="527.833"
                  y="399.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="572.333"
                  y="399.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="616.833"
                  y="399.528"
                  width="30.5"
                  height="4"
                  rx="1"
                  fill="#E5E7EB"
                />
                <rect
                  x="732.667"
                  y="320.528"
                  width="239.333"
                  height="92.6746"
                  rx="4"
                  fill="white"
                />
                <rect
                  x="742.667"
                  y="330.528"
                  width="40"
                  height="4"
                  rx="2"
                  fill="#9CA3AF"
                />
                <rect
                  x="958"
                  y="330.528"
                  width="4"
                  height="4"
                  rx="2"
                  fill="#D1D5DB"
                />
                <rect
                  x="742.667"
                  y="348.528"
                  width="113"
                  height="5"
                  rx="2.5"
                  fill="#E5E7EB"
                />
                <rect
                  x="742.667"
                  y="358.528"
                  width="92"
                  height="5"
                  rx="2.5"
                  fill="#E5E7EB"
                />
                <rect
                  x="742.667"
                  y="368.528"
                  width="109"
                  height="5"
                  rx="2.5"
                  fill="#E5E7EB"
                />
                <rect
                  x="742.667"
                  y="378.528"
                  width="64"
                  height="5"
                  rx="2.5"
                  fill="#E5E7EB"
                />
                <rect
                  x="742.667"
                  y="398.366"
                  width="4"
                  height="4"
                  rx="2"
                  fill="#9CA3AF"
                />
                <rect
                  x="746.667"
                  y="398.366"
                  width="4"
                  height="4"
                  rx="2"
                  fill="#9CA3AF"
                />
                <rect
                  x="750.667"
                  y="398.366"
                  width="4"
                  height="4"
                  rx="2"
                  fill="#9CA3AF"
                />
                <rect
                  x="932.482"
                  y="397.528"
                  width="29.5174"
                  height="5.67462"
                  rx="2"
                  fill="#E5E7EB"
                />
                <g clip-path="url(#clip1_2881_4057)">
                  <rect
                    x="214"
                    y="433.528"
                    width="495"
                    height="251"
                    rx="4"
                    fill="white"
                  />
                  <g clip-path="url(#clip2_2881_4057)">
                    <rect
                      x="230"
                      y="449.528"
                      width="73"
                      height="7"
                      rx="2"
                      fill="#9CA3AF"
                    />
                    <rect
                      x="230"
                      y="460.528"
                      width="39"
                      height="5"
                      rx="2"
                      fill="#D1D5DB"
                    />
                  </g>
                  <g opacity="0.3">
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M214 496.121C214 496.121 248.347 561.74 284.714 561.74C319.061 561.74 319.061 533.902 355.429 533.902C389.776 533.902 389.776 579.637 426.143 579.637C460.49 579.637 460.49 490.155 496.857 490.155C531.204 490.155 531.204 627.36 567.571 627.36C601.918 627.36 601.918 571.683 638.286 571.683C672.633 571.683 709 619.406 709 619.406V684.528H214V496.121Z"
                      fill="#C2C4C7"
                    />
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M214 496.121C214 496.121 248.347 561.74 284.714 561.74C319.061 561.74 319.061 533.902 355.429 533.902C389.776 533.902 389.776 579.637 426.143 579.637C460.49 579.637 460.49 490.155 496.857 490.155C531.204 490.155 531.204 627.36 567.571 627.36C601.918 627.36 601.918 571.683 638.286 571.683C672.633 571.683 709 619.406 709 619.406V684.528H214V496.121Z"
                      fill="url(#paint0_linear_2881_4057)"
                    />
                  </g>
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M496.858 487.528C513.269 487.528 520.472 503.518 533.283 552.658L535.859 562.558C547.654 607.295 555.473 624.115 567.572 624.115C579.423 624.115 586.116 618.193 597.473 600.642L599.276 597.827C612.918 576.44 621.348 568.688 638.286 568.688C640.257 568.688 642.252 568.83 644.268 569.107C658.76 571.1 673.83 579.901 688.933 593.303C694.883 598.581 700.429 604.227 705.431 609.873C706.744 611.355 707.676 612.757 708.765 614.062V615.8V617.566V622.748L704.652 618.228C703.347 616.631 701.834 614.848 700.132 612.928C695.269 607.438 689.877 601.95 684.114 596.836C669.907 584.23 655.869 576.031 643.173 574.285C641.513 574.057 639.883 573.942 638.286 573.942C625.603 573.942 618.433 579.868 607.164 596.992L602.165 604.731C590.264 622.821 582.426 629.368 567.572 629.368C549.966 629.368 542.186 612.352 528.896 561.399L525.752 549.347C514.803 508.201 507.564 492.782 496.858 492.782C484.198 492.782 476.675 503.51 464.62 533.551L461.564 541.203C449.324 571.425 441.757 581.86 426.144 581.86C410.434 581.86 401.976 576.19 389.696 560.936L384.179 553.933C373.597 540.776 367.027 536.331 355.429 536.331C341.866 536.331 334.722 539.356 323.123 548.251L320.827 550.038C307.7 560.327 299.737 564.044 284.715 564.044C267.307 564.044 249.974 551.308 232.988 530.185C227.169 522.949 221.808 515.211 217.022 507.473L215.104 504.32C214.8 503.812 214.504 503.312 214.216 502.821V498.174L214.313 490.331L216.999 495.068L218.51 497.81L220.104 500.575C220.95 502.015 221.866 503.537 222.85 505.128C227.534 512.701 232.78 520.272 238.452 527.326C254.298 547.031 270.302 558.791 284.715 558.791C297.093 558.791 303.701 555.947 314.812 547.416L317.045 545.677C330.675 534.994 339.192 531.078 355.429 531.078C370.456 531.078 378.379 536.793 390.814 552.543L392.789 555.074C405.302 571.188 412.476 576.607 426.144 576.607C437.334 576.607 444.284 566.894 455.38 539.552L459.192 530.025C471.926 498.607 480.068 487.528 496.858 487.528Z"
                    fill="#D1D5DB"
                  />
                </g>
                <g clip-path="url(#clip3_2881_4057)">
                  <rect
                    x="729"
                    y="433.528"
                    width="243"
                    height="251"
                    rx="4"
                    fill="white"
                  />
                  <rect
                    x="745"
                    y="449.528"
                    width="73"
                    height="7"
                    rx="2"
                    fill="#9CA3AF"
                  />
                  <rect
                    x="745"
                    y="460.528"
                    width="39"
                    height="5"
                    rx="2"
                    fill="#D1D5DB"
                  />
                  <path
                    d="M748 684.528C746.343 684.528 745 683.185 745 681.528V490.528C745 488.871 746.343 487.528 748 487.528H756C757.657 487.528 759 488.871 759 490.528V681.528C759 683.185 757.657 684.528 756 684.528H748Z"
                    fill="#D1D5DB"
                  />
                  <path
                    d="M767 684.528C765.343 684.528 764 683.185 764 681.528V549.528C764 547.871 765.343 546.528 767 546.528H775C776.657 546.528 778 547.871 778 549.528V681.528C778 683.185 776.657 684.528 775 684.528H767Z"
                    fill="#E5E7EB"
                  />
                  <path
                    d="M793 684.528C791.343 684.528 790 683.185 790 681.528V600.528C790 598.871 791.343 597.528 793 597.528H801C802.657 597.528 804 598.871 804 600.528V681.528C804 683.185 802.657 684.528 801 684.528H793Z"
                    fill="#D1D5DB"
                  />
                  <path
                    d="M812 684.528C810.343 684.528 809 683.185 809 681.528V528.528C809 526.871 810.343 525.528 812 525.528H820C821.657 525.528 823 526.871 823 528.528V681.528C823 683.185 821.657 684.528 820 684.528H812Z"
                    fill="#E5E7EB"
                  />
                  <path
                    d="M838 684.528C836.343 684.528 835 683.185 835 681.528V517.528C835 515.871 836.343 514.528 838 514.528H846C847.657 514.528 849 515.871 849 517.528V681.528C849 683.185 847.657 684.528 846 684.528H838Z"
                    fill="#D1D5DB"
                  />
                  <path
                    d="M857 684.528C855.343 684.528 854 683.185 854 681.528V496.528C854 494.871 855.343 493.528 857 493.528H865C866.657 493.528 868 494.871 868 496.528V681.528C868 683.185 866.657 684.528 865 684.528H857Z"
                    fill="#E5E7EB"
                  />
                  <path
                    d="M883 684.528C881.343 684.528 880 683.185 880 681.528V517.528C880 515.871 881.343 514.528 883 514.528H891C892.657 514.528 894 515.871 894 517.528V681.528C894 683.185 892.657 684.528 891 684.528H883Z"
                    fill="#D1D5DB"
                  />
                  <path
                    d="M902 684.528C900.343 684.528 899 683.185 899 681.528V602.528C899 600.871 900.343 599.528 902 599.528H910C911.657 599.528 913 600.871 913 602.528V681.528C913 683.185 911.657 684.528 910 684.528H902Z"
                    fill="#E5E7EB"
                  />
                  <path
                    d="M928 684.528C926.343 684.528 925 683.185 925 681.528V535.528C925 533.871 926.343 532.528 928 532.528H936C937.657 532.528 939 533.871 939 535.528V681.528C939 683.185 937.657 684.528 936 684.528H928Z"
                    fill="#D1D5DB"
                  />
                  <path
                    d="M947 684.528C945.343 684.528 944 683.185 944 681.528V571.528C944 569.871 945.343 568.528 947 568.528H955C956.657 568.528 958 569.871 958 571.528V681.528C958 683.185 956.657 684.528 955 684.528H947Z"
                    fill="#E5E7EB"
                  />
                </g>
              </g>
              <defs>
                <filter
                  id="filter0_dd_2881_4057"
                  x="-3"
                  y="-2"
                  width="998"
                  height="42"
                  filterUnits="userSpaceOnUse"
                  color-interpolation-filters="sRGB"
                >
                  <feFlood flood-opacity="0" result="BackgroundImageFix" />
                  <feColorMatrix
                    in="SourceAlpha"
                    type="matrix"
                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                    result="hardAlpha"
                  />
                  <feOffset dy="1" />
                  <feGaussianBlur stdDeviation="1.5" />
                  <feColorMatrix
                    type="matrix"
                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"
                  />
                  <feBlend
                    mode="normal"
                    in2="BackgroundImageFix"
                    result="effect1_dropShadow_2881_4057"
                  />
                  <feColorMatrix
                    in="SourceAlpha"
                    type="matrix"
                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                    result="hardAlpha"
                  />
                  <feOffset dy="1" />
                  <feGaussianBlur stdDeviation="1" />
                  <feColorMatrix
                    type="matrix"
                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.06 0"
                  />
                  <feBlend
                    mode="normal"
                    in2="effect1_dropShadow_2881_4057"
                    result="effect2_dropShadow_2881_4057"
                  />
                  <feBlend
                    mode="normal"
                    in="SourceGraphic"
                    in2="effect2_dropShadow_2881_4057"
                    result="shape"
                  />
                </filter>
                <linearGradient
                  id="paint0_linear_2881_4057"
                  x1="461.5"
                  y1="490.155"
                  x2="461.5"
                  y2="684.528"
                  gradientUnits="userSpaceOnUse"
                >
                  <stop offset="0.046875" stop-color="white" stop-opacity="0" />
                  <stop offset="1" stop-color="white" />
                </linearGradient>
                <clipPath id="clip0_2881_4057">
                  <rect width="992" height="715" fill="white" />
                </clipPath>
                <clipPath id="clip1_2881_4057">
                  <rect
                    x="214"
                    y="433.528"
                    width="495"
                    height="251"
                    rx="4"
                    fill="white"
                  />
                </clipPath>
                <clipPath id="clip2_2881_4057">
                  <rect
                    width="495"
                    height="16"
                    fill="white"
                    transform="translate(214 449.528)"
                  />
                </clipPath>
                <clipPath id="clip3_2881_4057">
                  <rect
                    x="729"
                    y="433.528"
                    width="243"
                    height="251"
                    rx="4"
                    fill="white"
                  />
                </clipPath>
              </defs>
            </svg>
            <div class="mt-4 md:mt-0">
              <h2
                class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900"
              >
                Congratulations! You're almost there.
              </h2>

              <hr />
              <p class="my-6 font-light text-gray-500 md:text-lg">
                To ensure proper functionality, please navigate to the plugin
                settings and enter the necessary contact information for the
                report.
              </p>
              <p class="mb-6 font-light text-gray-500 md:text-lg">
                To do this, follow these steps:
              </p>
              <ol class="space-y-1 mb-8 text-gray-500 list-disc list-inside">
                <li class="font-light text-gray-500 md:text-lg">
                  <strong>Go to the Plugins section</strong> by clicking on the
                  button below.
                </li>

                <li class="font-light text-gray-500 md:text-lg">
                  <strong>Locate the contact information fields</strong> in the
                  settings page.
                </li>
                <li class="font-light text-gray-500 md:text-lg">
                  <strong>Enter the necessary details</strong>, ensuring
                  accuracy for proper notifications and reporting.
                </li>
                <li class="font-light text-gray-500 md:text-lg">
                  <strong>Save your changes</strong> by clicking the
                  &quot;Save&quot; button at the top-right of the page.
                </li>
              </ol>

              <a
                target="_self"
                href="/wp-admin/admin.php?page=settings"
                class="inline-flex items-center bg-gray-300 text-black hover:text-blue-900 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
              >
                Go to Settings
                <svg
                  class="ml-2 -mr-1 w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  ></path>
                </svg>
              </a>
            </div>
          </div>
        </section>
      </template>
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
          <multiselect
            v-if="users.length"
            id="user"
            ref="multiRef"
            v-model="struckLogs.users"
            :multiple="true"
            :close-on-select="false"
            :hide-selected="true"
            :taggable="true"
            track-by="code"
            placeholder="Pick a user"
            label="name"
            :preselect-first="true"
            :options="users"
          ></multiselect>
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
        <!-- <div class="flex items-center gap-2">
          <p>Include Site Page Speed and metrics in the report?</p>
          <button
            @click="toggleState"
            class="toggle-button"
            :class="{ on: isOn, off: !isOn }"
          ></button>
        </div>
        <div class="flex items-center gap-2">
          <p>Include Email in the Logs Table?</p>
          <button
            @click="toggleEmailState"
            class="toggle-button"
            :class="{ on: isEmailOn, off: !isEmailOn }"
          ></button>
        </div> -->
        <button
          @click="exportToPDF"
          :disabled="loading"
          class="flex w-fit self-end items-center gap-4 text-white bg-green-700 disabled:bg-gray-400 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5"
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
          v-if="loading && isOn"
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
import Multiselect from "vue-multiselect";
import { onClickOutside } from "@vueuse/core";
import { useTemplateRef } from "vue";

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
const isOn = ref(
  window.struckData.plugin_options.export_settings.pageSpeed_on_export
);
const isEmailOn = ref(
  window.struckData.plugin_options.export_settings.email_on_export
);
const showModal = ref(false);
const multiSelect = useTemplateRef("multiRef");
const selectedOption = ref("");
const comment = ref("");
const { translateAction, renderColor } = useActions();

function hasRequiredSettings(settingsData) {
  if (!settingsData || typeof settingsData !== "object") return false;

  // Define each “path” of keys we want to check
  const requiredPaths = [
    ["visual_identity", "company_logo", "url"],
    ["contact_information"],
  ];

  return requiredPaths.every((path) => {
    let obj = settingsData;

    // Walk the path, checking existence at each step
    for (const key of path) {
      if (
        obj == null || // catches null/undefined
        !Object.prototype.hasOwnProperty.call(obj, key) // missing property
      ) {
        return false;
      }
      obj = obj[key];
    }

    // At this point, `obj` is the value at the end of the path.
    // Reject null/undefined
    if (obj == null) return false;

    // If it’s a string, reject empty or whitespace-only
    if (typeof obj === "string" && obj.trim() === "") return false;

    // (You could add more type‐specific checks here if needed.)

    return true;
  });
}

const closeMultiSelect = () => {
  multiSelect.value.deactivate();
};
onClickOutside(multiSelect, closeMultiSelect);

const api = axios.create({
  baseURL: `/wp-json/struck/v1/`,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
    "X-WP-Nonce": window.wpApiSettings.nonce,
  },
  timeout: 100000,
});

const submitEntry = () => {
  loading.value = true;

  if (comment.value && selectedOption.value) {
    api
      .post("manual-entry", {
        action_type: selectedOption.value,
        action_taken: comment.value,
      })
      .then(() => {
        showModal.value = false;
        comment.value = "";
        selectedOption.value = "";
        offset.value = 0;
        limit.value = 10;
        currentPage.value = 1;
        fetchLogs(offset.value, limit.value);
        loading.value = false;
      })
      .catch((error) => {
        console.error("Error adding manual entry:", error);
        alert("Failed to add manual entry. Please try again.");
      });
  }
};

const getPagespeedData = async () => {
  loading.value = true;
  const response = await api.get("pagespeed");
  window.struckData.page_speed_data = response.data;
};

const fetchUsers = async () => {
  try {
    const response = await api.get("users");
    response.data.unshift({ id: "0", name: "System events", email: "" });
    response.data.unshift({ id: "*", name: "All users", email: "" });
    const finalArray = response.data.map((user) => {
      return {
        id: user.id,
        name: user.name,
        email: user.email,
        code: user.email.substring(0, 2) + Math.floor(Math.random() * 10000000),
      };
    });
    users.value = finalArray;
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
        user_ids: id,
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

const today = new Date();

const struckLogs = ref({
  // today's date
  startDate:
    today.getFullYear() +
    "-" +
    String(today.getMonth() + 1).padStart(2, "0") +
    "-" +
    String(today.getDate()).padStart(2, "0"),
  endDate:
    today.getFullYear() +
    "-" +
    String(today.getMonth() + 1).padStart(2, "0") +
    "-" +
    String(today.getDate()).padStart(2, "0"),
  reportType: "",
  activity: "",
  users: [],
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
  if (!color) return;
  const hex = color.replace("#", "");
  const c_r = parseInt(hex.substring(0, 2), 16);
  const c_g = parseInt(hex.substring(2, 4), 16);
  const c_b = parseInt(hex.substring(4, 6), 16);
  const brightness = (c_r * 299 + c_g * 587 + c_b * 114) / 1000;
  return brightness > 155;
}

const backgroundColor = "#0078FF";
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
  if (isOn.value) {
    await getPagespeedData();
  } else {
    loading.value = true;
  }

  await fetchLogs(
    0,
    10,
    struckLogs.value.users ? struckLogs.value.users : null,
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
        background: #0F1010;
        text-align: center;
        padding: 0px;
      ">
          <table style="width: 100%; border-collapse: collapse;" cellpadding="20">
              <tr>
                  <td>
                     <div style="z-index: 1; text-align: left; width: 140px; margin-right: auto; margin-left: 0;">
                          <img style="width: 100%; max-width: 140px; margin-left: 0; height: auto; object-fit: contain;" src="data:image/png;base64,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"/>
                      </div>
                  </td>
                  <td style="text-align: right">

                      <div style="z-index: 1; text-align: center; width: 160px; margin-right: 0px; margin-left: auto;">
                          <img style="width: 100%; max-width: 160px; margin: 0 auto; height: auto; object-fit: contain;" src="${settingsData?.visual_identity?.company_logo?.url}"/>
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
  title.style.borderBottom = `5px solid #0F1010`;

  // Information
  const projectSummary = document.createElement("div");
  projectSummary.style.marginBottom = "20px";
  projectSummary.style.paddingTop = "20px";

  let contactRows = "";
  settingsData?.contact_information.forEach((contact, index) => {
    contactRows += `
    <td style="vertical-align: top; padding-right: 20px; font-size: 10px; ${
      index !== 0 ? "border-left: 1px solid #DFDFE5; padding-left: 20px;" : ""
    }">
      <strong>${contact.contact_type}:<br /></strong>
      ${contact.contact_name}<br />

      <strong>Contact Email:</strong>
      <a style="color: #0078FF;" href="mailto:${contact.contact_email}">
        ${contact.contact_email}
      </a>
    </td>
  `;
  });

  // Wrap the cells in a single row
  const contactTable = `
  <table style="width: 100%; border-collapse: collapse;">
    <tr>${contactRows}</tr>
  </table>
`;

  projectSummary.innerHTML = `
  <div style="border-bottom: 1px solid #0F1010; margin-bottom: 10px; padding-bottom: 3px; display: flex; justify-content: space-between; align-items: center;">
    <h3 style="margin: 0; font-size: 12px;">Project Information</h3>
    <p style="margin: 0; font-size: 12px;"><strong>Report Date:</strong> ${new Date().toLocaleDateString()}</p>
  </div>

  ${contactTable}

<div style="background: #EEEDED;" class="flex items-center px-4 pt-1 pb-2 mt-10 text-sm text-blue-800 rounded-lg text-['#363939']" role="alert">
  <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
  </svg>
  <span class="sr-only">Info</span>
  <p style="font-size: 10px; color: #363939; margin-top: 1rem;">
    If you have any questions, concerns, or require clarification regarding the information provided in this report, please do not hesitate to contact us at the email addresses listed above. We are happy to assist and provide any additional details you may need.
  </p>
</div>
`;

  // Summary
  const statusSummary = document.createElement("div");
  statusSummary.style.marginBottom = "20px";
  statusSummary.style.paddingTop = "20px";
  statusSummary.innerHTML = `
    <h3 style="font-size: 12px; border-bottom: 1px solid #0F1010; margin-bottom: 10px; padding-bottom: 5px">Summary</h3>
    <p>${struckLogs.value.summary}</p>
  `;

  // Table start
  const projectOverview = document.createElement("div");
  projectOverview.style.marginBottom = "20px";
  projectOverview.style.paddingTop = "20px";
  projectOverview.innerHTML = `
    <h3 style="font-size: 12px; border-bottom: 1px solid #0F1010; margin-bottom: 10px; padding-bottom: 5px">Logs</h3>
  `;

  // const tableElement = document.getElementById("export-table");
  const logsTableContainer = document.createElement("div");
  if (!filteredLogs.value.length) {
    alert("No data available to export.");
    return;
  }

  logsTableContainer.innerHTML = isEmailOn.value
    ? `
  <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; text-align: left">
    <thead style="background-color: ${backgroundColor}; color: ${textColor}">
      <tr>
        <th style="border: 1px solid #DFDFE5; padding: 8px;">Name</th>
        <th style="border: 1px solid #DFDFE5; padding: 8px;">Email</th>
        <th style="border: 1px solid #DFDFE5; padding: 8px;">Role</th>
        <th style="border: 1px solid #DFDFE5; padding: 8px;">Action</th>
        <th style="border: 1px solid #DFDFE5; padding: 8px;">Action Taken</th>
        <th style="border: 1px solid #DFDFE5; padding: 8px; width: 120px;">Action Time</th>
      </tr>
    </thead>
    <tbody>
      ${reportLogs.value
        .map((log) => {
          const customClass =
            renderColor(log?.action_type) === "green"
              ? "bg-green-500/20 border border-green-900 text-green-900"
              : renderColor(log?.action_type) === "red"
              ? "bg-red-50 border border-red-900 text-red-900"
              : "bg-yellow-100 border border-yellow-900 text-yellow-900";
          return `
          <tr>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px;">${
              log?.user_name || "-"
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px;">${
              log?.email || "-"
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px;">${
              log?.role || "-"
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px;">
              <div class="w-max">
                <div class="relative grid items-center px-2 py-1 font-sans text-xs font-bold uppercase rounded-md select-none whitespace-nowrap
                  ${customClass}">
                  <span>${translateAction(log?.action_type)}</span>
                </div>
              </div>
            </td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px;">${
              log?.action_taken || "-"
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px;">${
              log?.action_time || "-"
            }</td>
          </tr>
        `;
        })
        .join("")}
    </tbody>
  </table>
`
    : `
  <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; text-align: left">
    <thead style="color: ${textColor}">
      <tr>
        <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px; border-top-left-radius: 4px;">Name</th>
        <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px;">Role</th>
        <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px;">Action</th>
        <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px;">Action Taken</th>
        <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px; width: 120px; border-top-right-radius: 4px;">Action Time</th>
      </tr>
    </thead>
    <tbody>
      ${reportLogs.value
        .map((log, index) => {
          const isLast = index === reportLogs.value.length - 1;
          const customBorder =
            renderColor(log?.action_type) === "green"
              ? "#22543d"
              : renderColor(log?.action_type) === "red"
              ? "#7f1d1d"
              : "#eba900";
          return `
          <tr>
            <td class="${
              isLast ? "rounded-bl-lg" : ""
            }" style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px;">${
            log?.user_name || "-"
          }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px;">${
              log?.role || "-"
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px;">
              <div class="w-max">
                <div style="border: 1px solid ${customBorder};" class="relative grid items-center px-2 py-1 font-sans text-xs font-bold uppercase rounded-md select-none whitespace-nowrap
                  ${
                    renderColor(log?.action_type) === "green"
                      ? "bg-green-500/20 border border-green-900 text-green-900"
                      : renderColor(log?.action_type) === "red"
                      ? "bg-red-50 border border-red-900 text-red-900"
                      : "bg-yellow-100 border border-yellow-900 text-yellow-900"
                  }">
                  <span>${translateAction(log?.action_type)}</span>
                </div>
              </div>
            </td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px;">${
              log?.action_taken || "-"
            }</td>
            <td class="${
              isLast ? "rounded-br-lg" : ""
            }" style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px;">${
            log?.action_time || "-"
          }</td>
          </tr>
        `;
        })
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
    <h3 style="font-size: 12px; border-bottom: 1px solid #0F1010; margin-bottom: 10px; padding-bottom: 5px">WordPress repository installed plugins</h3>
  `;

  const pluginsTableContainer = document.createElement("div");
  pluginsTableContainer.innerHTML = `
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; text-align: left">
        <thead style="color: ${textColor}">
            <tr>
                <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px; border-top-left-radius: 4px;">Plugin Name</th>
                <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px;">WP Required Version</th>
                <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px;">Tested Up To</th>
                <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px;">Requires PHP</th>
                <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px;">Rating</th>
                <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px; border-top-right-radius: 4px;">Last Repo Update</th>
            </tr>
        </thead>
    <tbody>
        ${pluginsData
          .map((plugin, index) => {
            const isLast = index === pluginsData.length - 1;
            return `
          <tr>
            <td class="${
              isLast ? "rounded-bl-lg" : ""
            }"  style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px">${
              plugin.plugin_name
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px">${
              plugin.WP_required_version
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px">${
              plugin.tested_up_to_WP_version
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px">${
              plugin.requires_php
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px">${
              plugin.rating
            }%</td>
            <td class="${
              isLast ? "rounded-br-lg" : ""
            }" style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px">${
              plugin.last_repo_update
            }</td>
          </tr>
        `;
          })
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
  wordfence.innerHTML = `${
    wordfenceData?.issues !== 0 &&
    wordfenceData?.issues?.new &&
    wordfenceData?.issues?.new?.length > 0
      ? `
    <h3 style="font-size: 12px; border-bottom: 1px solid #0F1010; margin-bottom: 10px; padding-bottom: 5px">Virus Scan (Powered by Wordfence™)</h3>
    <p style="font-size: 10px;"><b>*${
      wordfenceData?.lastMessage
        ? wordfenceData?.lastMessage
        : "No scan has been run lately"
    }</b></p>`
      : ""
  }`;

  const wordfenceTableContainer = document.createElement("div");
  wordfenceTableContainer.innerHTML = `${
    wordfenceData?.issues !== 0 &&
    wordfenceData?.issues?.new &&
    wordfenceData?.issues?.new?.length > 0
      ? `
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; text-align: left">
        <thead style="color: ${textColor}">
            <tr>
                <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px; border-top-left-radius: 4px;">Plugin Name</th>
                <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px;">Version</th>
                <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px;">Available Update</th>
                <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px;">New Version</th>
                <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px;">Date</th>
                <th style="background-color: ${backgroundColor}; border: 1px solid #DFDFE5; padding: 8px; border-top-right-radius: 4px;">Message</th>
            </tr>
        </thead>
    <tbody>
        ${wordfenceData?.issues?.new
          .map((plugin, index) => {
            const isLast = index === wordfenceData?.issues?.new.length - 1;
            return `
          <tr>
            <td class="${
              isLast ? "rounded-bl-lg" : ""
            }" style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px">${
              plugin?.type.includes("wfPluginUpgrade") ||
              plugin?.type.includes("wfPluginVulnerable")
                ? plugin?.data?.Title
                : plugin?.type.includes("wfThemeUpgrade")
                ? plugin?.data?.Name
                : "-"
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px">${
              plugin?.type.includes("wfPluginUpgrade") ||
              plugin?.type.includes("wfPluginVulnerable")
                ? plugin?.data?.Version
                : plugin?.type.includes("wfThemeUpgrade")
                ? plugin?.data?.version
                : plugin?.type.includes("wfUpgrade")
                ? plugin?.data?.currentVersion
                : "-"
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px">${
              plugin?.type.includes("wfPluginUpgrade") ||
              plugin?.type.includes("wfPluginVulnerable")
                ? plugin?.data?.updateAvailable
                  ? "Yes"
                  : "No"
                : plugin?.type.includes("wfThemeUpgrade")
                ? parseFloat(plugin?.data?.newVersion) >
                  parseFloat(plugin?.data?.version)
                  ? "Yes"
                  : "No"
                : plugin?.type.includes("wfUpgrade")
                ? parseFloat(plugin?.data?.newVersion) >
                  parseFloat(plugin?.data?.currentVersion)
                  ? "Yes"
                  : "No"
                : "-"
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px">${
              plugin?.data?.newVersion ? plugin?.data?.newVersion : "-"
            }</td>
            <td style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px">${
              plugin?.displayTime ? plugin?.displayTime : "-"
            }</td>
            <td class="${
              isLast ? "rounded-br-lg" : ""
            }" style="border: 1px solid #DFDFE5; padding: 8px; font-size: 11px">${
              plugin?.shortMsg ? plugin?.shortMsg : "-"
            }</td>
          </tr>
        `;
          })
          .join("")}
      </tbody>
    </table>
  `
      : ""
  }`;
  // }

  //   Page Speed Heading
  const pageSpeedDataHeading = document.createElement("div");
  pageSpeedDataHeading.style.marginTop = "20px";
  pageSpeedDataHeading.style.paddingTop = "20px";
  pageSpeedDataHeading.innerHTML = `
    <h3 style="font-size: 12px; border-bottom: 1px solid #0F1010; margin-bottom: 10px; padding-bottom: 5px">Site Page Speed and metrics (Powered by PageSpeed Insights™)</h3>
  `;

  // Page Speed Data
  const pageSpeedData = document.createElement("div");
  pageSpeedData.style.marginBottom = "20px";
  pageSpeedData.style.paddingTop = "20px";
  pageSpeedData.innerHTML = !isOn.value
    ? ""
    : `
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
            <div class=" w-6/12">
                <div class="flex flex-col">
                    <h3 class="font-bold text-lg mb-2">Metrics breakdown:</h3>
                    <div class="grid grid-cols-1 gap-2">
                      <div class="grid grid-cols-2 gap-2">
                        <div>
                          <h4 class="font-bold mb-1">Performance</h4>
                          <p>This is the overall score that Google gives your webpage based on how fast and smooth it feels to users.</p>
                        </div>
                        <div>
                          <h4 class="font-bold mb-1">Comulative Layout Shift</h4>
                          <p>This measures how much the page content moves around while it's loading. A low CLS means the page is stable as it loads.</p>
                        </div>
                      </div>
                      <div class="grid grid-cols-2 gap-2">
                        <div>
                          <h4 class="font-bold mb-1">First Contentful Paint</h4>
                          <p>This is the time it takes for the first piece of content to appear on the screen after someone starts loading the page.</p>
                        </div>
                        <div>
                          <h4 class="font-bold mb-1">Largest Contentful Paint</h4>
                          <p>This measures how long it takes for the biggest visible element (like a Hero section or text) to appear.</p>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap w-6/12 gap-4 items-center">
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
                      window.struckData?.page_speed_data?.lighthouseResult
                        ?.audits["first-contentful-paint"].score * 100
                    )};"></div>
                    </div>
                    <div class="chart-info">
                    <h4>FCP</h4>
                    <p>First Contentful Paint</p>
                    </div>
                </div>
                <div class="time-breakdown-chart ${scoreColor(
                  window.struckData?.page_speed_data?.lighthouseResult.audits[
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
    <h3 style="font-size: 12px; border-bottom: 1px solid #0F1010; margin-bottom: 10px; padding-bottom: 5px">Recommendations</h3>
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
      color: #000000;
      text-align: center;
      padding: 10px 0;
      border-top: 2px solid #DFDFE5;
      margin-top: 100px;
    ">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td>
                    <div style="color: #000000; z-index: 1; text-align: left">
                        Developed by Struck
                    </div>
                </td>
                <td style:"text-align: right">
                    <div style="z-index: 1; text-align: right">
                        <img style="width: 160px; height: auto; object-fit: contain;" src="data:image/png;base64,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" />
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
  if (struckLogs.value.summary) {
    wrapperElement.appendChild(statusSummary);
  }
  wrapperElement.appendChild(projectOverview);
  wrapperElement.appendChild(logsTableContainer);
  wrapperElement.appendChild(installedPluginsHeader);
  wrapperElement.appendChild(pluginsTableContainer);
  wrapperElement.appendChild(wordfence);
  wrapperElement.appendChild(wordfenceTableContainer);

  if (isOn.value) {
    wrapperElement.appendChild(pageSpeedDataHeading);
    wrapperElement.appendChild(pageSpeedData);
  }

  if (struckLogs.value.recommendations) {
    wrapperElement.appendChild(recommendations);
  }
  wrapperElement.appendChild(footer);

  const exportedDocument = document.querySelector(".export");

  exportedDocument.appendChild(wrapperElement);

  const { width, height } = exportedDocument.getBoundingClientRect();

  // Save
  const options = {
    margin: [10, 10],
    filename: `${window.location.hostname
      .replaceAll("/", "")
      .replaceAll("https:", "")} Status Report ${new Date()
      .toLocaleDateString()
      .replace(/\//g, "-")}.pdf`,
    html2canvas: { scale: 2 },
    pageBreak: {
      mode: ["avoid-all"],
      autoPaging: false,
    },
    jsPDF: {
      unit: "px",
      format: [width, height + height * 0.35],
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
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
.multiselect__tags input#user {
  display: none;
}
.multiselect__content-wrapper {
  box-shadow: -4px 9px 14px 9px #0000001c;
}
</style>
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

.toggle-button {
  padding: 10px 20px;
  border: none;
  border-radius: 30px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.3s ease;
  position: relative;

  &:after {
    @apply absolute bg-white rounded-full shadow-md h-[calc(100%-4px)] w-[calc(50%-0px)] top-[2px] right-[2px] transition-transform duration-300 ease-in-out;
    content: "";
  }

  &.on {
    background-color: #4caf50;
    /* Green */
    color: white;
  }

  &.off {
    background-color: #f44336;
    /* Red */
    color: white;

    &:after {
      @apply transform -translate-x-[calc(100%_-_4px)];
    }
  }

  &:hover {
    opacity: 0.8;
  }
}

.animate-pulse {
  animation: pulse 1.5s infinite;
}
</style>
