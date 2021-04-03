# Test task

### Exists
There are 3 different microservices, each with its own settings interface. Where there can be completely different fields, different validations, etc. Including, there may be different protocols for communicating with these microservices.

The first microservice is REST API, and the settings for it are the fields:
- [field1: string, field2: bool, field3: array<string>]

The second microservice works only by gRPC, and the settings for it are the fields:
- [field1: string, field2: bool, field3: int]

The third microservice works only by http, and the settings for it are the fields:
- [field1: bool, field2: int, field3: array<string, int>]

### Need to do
Write a service that will receive available settings from these microservices and display them on the page for reading and writing.
It is enough to implement the logic of working with the services themselves. The task needs to be done using the Symfony framework.
Other modern frameworks can be used (Laravel, Slim, etc.)

The code doesn't have to be runnable and 100% working. There is no need to implement three services and work on gRPC. Tests are not
are required but welcome. First of all, OOP understanding and ability to work with the framework will be assessed.
