import http from 'k6/http';

export const options = {
  vus: 50,
  duration: '120s',
};

export default function () {
  http.get(`http://${__ENV.ALB}/api/test`);
}
