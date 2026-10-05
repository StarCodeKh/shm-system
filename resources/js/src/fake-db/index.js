import mock from './mock'


import "./data/data-list/index.js"
import "./data/user-management/index.js"
import "./data/user/index.js"


mock.onAny().passThrough(); // forwards the matched request over network
