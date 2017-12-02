import webpack from 'webpack';
import ExtractTextPlugin from 'extract-text-webpack-plugin';

// Paths
const paths = {
  base: __dirname,
};

module.exports = {
  entry: [
    './resources/assets/scripts/app.js',
    './resources/assets/styles/app.scss',
  ],
  output: {
    filename: './public/static/js/app.js',
  },
  module: {
    rules: [
      {
          test: /\.jsx?$/,
          loader: 'babel-loader',
          exclude: /node_modules/
      },
      {
        test: /\.scss$/,
        loader: ExtractTextPlugin.extract([
          'css-loader',
          'sass-loader',
        ]),
      },
    ],
  },
  plugins: [
    new ExtractTextPlugin({
      filename: './public/static/css/app.css',
    }),
  ],
  devtool: 'source-map',
};
