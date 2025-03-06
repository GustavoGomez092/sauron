<template>
  <div class="max-w-full mx-auto mt-[10px] mr-[20px] mb-0 ml-[2px]">
    <div
      class="relative flex flex-col w-full h-full text-slate-700 bg-white shadow-md rounded-xl"
    >
      <template
        v-if="
          settingsData &&
          settingsData.color_skin &&
          settingsData.company_logo?.url &&
          settingsData.address_information &&
          settingsData.assigned_manager &&
          settingsData.development_director &&
          settingsData.contact_email &&
          settingsData.assigned_manager_email
        "
      >
        <div class="relative mx-4 mt-4">
          <div class="flex items-center justify-between">
            <div class="flex flex-row gap-7 items-center">
              <div>
                <h3 class="text-lg font-semibold text-slate-800">
                  Site maintenance report
                </h3>
                <p class="text-slate-500">
                  Review each log or download the file
                </p>
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
                            ? 'bg-green-500/20 text-green-900'
                            : renderColor(user.action_type) === 'red'
                            ? 'bg-red-50 text-red-900'
                            : 'bg-yellow-50 text-yellow-900'
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
        <div class="flex items-center gap-2">
          <p>Include Site Page Speed and metrics in the report?</p>
          <button
            @click="toggleState"
            class="toggle-button"
            :class="{ on: isOn, off: !isOn }"
          ></button>
        </div>
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
const isOn = ref(true);
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

const toggleState = () => {
  isOn.value = !isOn.value;
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
  if (!color) return;
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
  if (isOn.value) {
    await getPagespeedData();
  } else {
    loading.value = true;
  }

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
  title.style.borderBottom = `5px solid ${settingsData.color_skin}`;

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
    <td style="vertical-align: top;">
      <strong>Development Director:</strong> ${
        settingsData.development_director
      }
      <br />
      <strong>Contact Email:</strong> 
      <a href="mailto:${settingsData.contact_email}">
        ${settingsData.contact_email}
      </a>
    </td>

    <td style="vertical-align: top;">
      <strong>Technical Producer:</strong> ${settingsData.assigned_manager}
      <br />
      <strong>Contact Email:</strong> 
      <a href="mailto:${settingsData.assigned_manager_email}">
        ${settingsData.assigned_manager_email}
      </a>
    </td>

    <td style="vertical-align: top;">
      <strong>Report Date:</strong> ${new Date().toLocaleDateString()}
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
        <th style="border: 1px solid #ccc; padding: 8px; width: 120px;">Action Time</th>
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
                  ${
                    renderColor(log?.action_type) === "green"
                      ? "bg-green-500/20 text-green-900"
                      : renderColor(log?.action_type) === "red"
                      ? "bg-red-50 text-red-900"
                      : "bg-yellow-50 text-yellow-900"
                  }">
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
  wordfence.innerHTML = `${
    wordfenceData?.issues !== 0 &&
    wordfenceData?.issues?.new &&
    wordfenceData?.issues?.new?.length > 0
      ? `
    <h3 style="border-bottom: 1px solid ${backgroundColor}; margin-bottom: 10px; padding-bottom: 5px">Virus Scan (Powered by Wordfence™)</h3>
    <p><b>*${
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
              plugin?.type.includes("wfPluginUpgrade") ||
              plugin?.type.includes("wfPluginVulnerable")
                ? plugin?.data?.Title
                : plugin?.type.includes("wfThemeUpgrade")
                ? plugin?.data?.Name
                : "-"
            }</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${
              plugin?.type.includes("wfPluginUpgrade") ||
              plugin?.type.includes("wfPluginVulnerable")
                ? plugin?.data?.Version
                : plugin?.type.includes("wfThemeUpgrade")
                ? plugin?.data?.version
                : plugin?.type.includes("wfUpgrade")
                ? plugin?.data?.currentVersion
                : "-"
            }</td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 11px">${
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
  `
      : ""
  }`;
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
    background-color: #4caf50; /* Green */
    color: white;
  }
  &.off {
    background-color: #f44336; /* Red */
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
