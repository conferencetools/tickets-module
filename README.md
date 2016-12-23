# opentickets
Open source ticketing system

This is a ticketing system MVP I built for PHP Yorkshire. It is unlikely you will be able to make use of it in it's 
current state, however the plan for the long run is to do for ticketing what opencfp has done for cfp's.

# Install (dev)

copy the .dist files in config to files without the .dist in the name; edit the config to your liking 
(Doctrine shouldn't need changing if you use the docker env), do a composer install (requires php 7) 
then run `make run` from the root directory. 
