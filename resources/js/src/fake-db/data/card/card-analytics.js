import mock from "@/fake-db/mock.js"

const data = {
  subscribers_gained: {
    analyticsData: [
        { device: 'វិស័យសេវាសេដ្ឋកិច្ច', icon: 'MonitorIcon', color: 'primary', sessionsPercentage: 58.6, comparedResultPercentage: 2 },
        { device: 'វិស័យសេវាសង្គមកិច្ច', icon: 'SmartphoneIcon', color: 'warning', sessionsPercentage: 34.9, comparedResultPercentage: 8 },
        { device: 'វិស័យសាធារណៈ', icon: 'TabletIcon', color: 'danger', sessionsPercentage: 6.5, comparedResultPercentage: -5 },
    ],
    series: [58.6, 34.9, 6.5]
  },
  productsOrders: {
    analyticsData: [
        { 'orderType': 'Finished', 'counts': 23043, color: 'primary' },
        { 'orderType': 'Pending', 'counts': 14658, color: 'warning' },
        { 'orderType': 'Rejected ', 'counts': 4758, color: 'danger' },
    ],
    series: [70, 52, 26],
  },
  customers: {
    analyticsData: [
        { 'customerType': 'វិស័យសេវាសេដ្ឋកិច្ច', 'counts': '១៣,៩៩៨,៧៤៤ ៛', color: 'primary' },
        { 'customerType': 'វិស័យសេវាសង្គមកិច្ច', 'counts': '១៣,៩៩៨,៧៤៤ ៛', color: 'warning' },
        { 'customerType': 'វិស័យសាធារណៈទូទៅ ', 'counts': '១៣,៩៩៨,៧៤៤ ៛', color: 'danger' },
    ],
    series: [690, 258, 149],
  },
  officerStatus: {
    analyticsData: [
        { 'customerType': 'សកម្ម', 'counts': '១៣,៩៩៨,៧៤៤ ៛', color: 'primary' },
        { 'customerType': 'អសកម្ម/ឈប់', 'counts': '១៣,៩៩៨,៧៤៤ ៛', color: 'warning' },
        { 'customerType': 'ស្លាប់ ', 'counts': '១៣,៩៩៨,៧៤៤ ៛', color: 'danger' },
        { 'customerType': 'ចូលនិវត្ត ', 'counts': '១៣,៩៩៨,៧៤៤ ៛', color: 'danger' },
        { 'customerType': 'ចូលនិវត្ត ', 'counts': '១៣,៩៩៨,៧៤៤ ៛', color: 'danger' },
    ],
    series: [690, 258, 149],
  },
  salesRadar: {
    series: [{
        name: 'Visits',
        data: [90, 50, 86, 40, 100, 20],
    }, {
        name: 'Sales',
        data: [70, 75, 70, 76, 20, 85],
    }],
  },
  supportTracker: {
    analyticsData: {
        openTickets: 163,
        meta: {
          "New Tickets": 29,
          "Open Tickets": 63,
          "Response Time": '1d',
        }
    },
    series: [83],
  },
  revenueComparisonLine: {
    analyticsData: {
      thisMonth: 86589,
      lastMonth: 73683
    },
    series: [{
            name: "ចំណូល",
            data: [45000, 47000, 44800, 47500, 45500, 48000, 46500, 48600]
        },
        {
            name: "ចំណាយ",
            data: [46000, 48000, 45500, 46600, 44500, 46500, 45000, 47000]
        }
    ],
  },
  goalOverviewRadialBar: {
    analyticsData: {
      completed: 786617,
      inProgress: 13561
    },
    series: [100]
  },
  salesBarSession: {
    series: [{
        name: 'Sessions',
        data: [75, 125, 225, 175, 125, 75, 25]
    }],
    analyticsData: {
      session: 2700,
      comparison : {
        str: "Last 7 Days",
        result: +5.2
      }
    }
  },
  todoToday: {
    date: "Sat, 16 Feb",
    numCompletedTasks: 2,
    totalTasks: 10,
    tasksToday: [
      {
        id: 3,
        task: "Refactor button component",
        date: "16 Feb 2019",
      },
      {
        id: 70,
        task: "Submit report to admin",
        date: "16 Feb 2019",
      },
      {
        id: 8,
        task: "Prepare presentation",
        date: "16 Feb 2019",
      },
      {
        id: 1,
        task: "Calculate monthly income",
        date: "16 Feb 2019",
      }
    ]
  },
  salesLine: {
    series: [{
        name: "Sales",
        data: [140, 180, 150, 205, 160, 295, 125, 255, 205, 305, 240, 295]
    }],
  },
  funding: {
    currBalance: 22597,
    depostis: 20065,
    comparison: {
      resultPerc: 5.2,
      pastData: 956
    },
    meta: {
      earned: {
        val: 56156,
        progress: 50
      },
      duration: {
        val: "2 Year",
        progress: 50
      }
    }
  },
  browserAnalytics: [{
      id: 1,
      name: 'វិស័យសេវាសេដ្ឋកិច្ច',
      ratio: 73,
      time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
      comparedResult: '800'
    },
    {
      id: 3,
      name: 'វិស័យសេវាសង្គមកិច្ច',
      ratio: 8,
      time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
      comparedResult: '-200'
    },
    {
      id: 2,
      name: 'វិស័យសាធារណៈទូទៅ',
      ratio: 19,
      time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
      comparedResult: '100'
    },
    {
      id: 4,
      name: 'វិស័យការពារជាតិ សន្តិសុខ',
      ratio: 27,
      time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
      comparedResult: '-450'
    },
  ],
  clientRetention: {
    series: [{
        name: 'ប្រាក់បើកផ្តល់',
        data: ['85%', '85%', '85%', '85%', '85%', '85%', '85%', '85%', '85%', '85%', '85%', '85%']
    }, {
        name: 'ប្រាក់ពន្ធ',
        data: ['85%', '85%', '85%', '85%', '85%', '85%', '85%', '85%', '85%', '85%', '85%', '85%']
    }],
  }
}


mock.onGet("/api/card/card-analytics/session-by-device").reply(() => {
  return [200, data.subscribers_gained];
});

mock.onGet("/api/card/card-analytics/products-orders").reply(() => {
  return [200, data.productsOrders];
});

mock.onGet("/api/card/card-analytics/customers").reply(() => {
  return [200, data.customers];
});

mock.onGet("/api/card/card-analytics/sales/radar").reply(() => {
  return [200, data.salesRadar];
});

mock.onGet("/api/card/card-analytics/support-tracker").reply(() => {
  return [200, data.supportTracker];
});

mock.onGet("/api/card/card-analytics/revenue-comparison").reply(() => {
  return [200, data.revenueComparisonLine];
});

mock.onGet("/api/card/card-analytics/goal-overview").reply(() => {
  return [200, data.goalOverviewRadialBar];
});

mock.onGet("/api/card/card-analytics/sales/bar").reply(() => {
  return [200, data.salesBarSession];
});

mock.onGet("/api/card/card-analytics/todo/today").reply(() => {
  return [200, data.todoToday];
});

mock.onGet("/api/card/card-analytics/sales/line").reply(() => {
  return [200, data.salesLine];
});

mock.onGet("/api/card/card-analytics/funding").reply(() => {
  return [200, data.funding];
});

mock.onGet("/api/card/card-analytics/browser-analytics").reply(() => {
  return [200, data.browserAnalytics];
});

mock.onGet("/api/card/card-analytics/client-retention").reply(() => {
  return [200, data.clientRetention];
});
