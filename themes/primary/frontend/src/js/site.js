import $ from 'jquery';
import '@/vendors';
import * as plugins from '@/plugins';
import * as modules from '@/modules';

$(function () {
	Object.values(plugins).forEach((factory) => factory());
	Object.values(modules).forEach((factory) => factory());
});
