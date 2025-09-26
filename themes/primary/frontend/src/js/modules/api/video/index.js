import YouTube from '@/modules/api/video/youtube';
import Vimeo from '@/modules/api/video/vimeo';

const API_LOOKUP = {
	vimeo: Vimeo,
	youtube: YouTube
};

export const getVideoAPIByType = (slug) => API_LOOKUP?.[slug];

export { YouTube, Vimeo };
